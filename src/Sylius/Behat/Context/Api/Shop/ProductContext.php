<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Shop;

use ApiPlatform\Core\Bridge\Symfony\Routing\IriConverter;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\Request;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var IriConverter */
    private $iriConverter;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage,
        IriConverter $iriConverter
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
        $this->iriConverter = $iriConverter;
    }

    /**
     * @When /^I check (this product)'s details$/
     * @When I view product :product
     */
    public function iOpenProductPage(ProductInterface $product): void
    {
        $this->client->show($product->getCode());
    }

    /**
     * @When I browse products from taxon :taxon
     */
    public function iBrowseProductsFromTaxon(TaxonInterface $taxon): void
    {
        $this->client->index();
        $this->client->addFilter('productTaxons.taxon.code', $taxon->getCode());
        $this->client->filter();
    }

    /**
     * @When I clear filter
     */
    public function iClearFilter(): void
    {
        $this->client->clearParameters();
        $this->client->filter();
    }

    /**
     * @When I search for products with name :name
     */
    public function iSearchForProductsWithName(string $name)
    {
        $this->client->addFilter('translations.name', $name);
        $this->client->filter();
    }

    /**
     * @Then I should see the product :name
     */
    public function iShouldSeeTheProduct(string $name): void
    {
        Assert::true($this->hasProductWithName(
            $this->responseChecker->getCollection($this->client->getLastResponse()),
            $name
        ));
    }

    /**
     * @Then I should not see the product :name
     */
    public function iShouldNotSeeTheProduct(string $name): void
    {
        Assert::false($this->hasProductWithName(
            $this->responseChecker->getCollection($this->client->getLastResponse()),
            $name
        ));
    }

    /**
     * @Then /^I should see the product price ("[^"]+")$/
     */
    public function iShouldSeeTheProductPrice(int $price): void
    {
        Assert::true(
            $this->hasProductWithPriceInChannel(
                [$this->responseChecker->getResponseContent($this->client->getLastResponse())],
                $price,
                $this->sharedStorage->get('channel'),
            )
        );
    }

    /**
     * @Then /^I should see the (product "[^"]+") with price ("[^"]+")$/
     */
    public function iShouldSeeTheProductWithPrice(ProductInterface $product, int $price): void
    {
        Assert::true(
            $this->hasProductWithPriceInChannel(
                $this->responseChecker->getCollection($this->client->getLastResponse()),
                $price,
                $this->sharedStorage->get('channel'),
                $product->getCode()
            ),
            sprintf("There is no product with %s code and %s price", $product->getCode(), $price)
        );
    }

    /**
     * @Then I should see the product name :name
     */
    public function iShouldSeeProductName(string $name): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithTranslation(
                $this->client->getLastResponse(),
                'en_US',
                'name',
                $name
            )
        );

        Assert::same($this->responseChecker->getTranslationValue($this->client->getLastResponse(), 'name'), $name);
    }

    /**
     * @Then its current variant should be named :variantName
     */
    public function itsCurrentVariantShouldBeNamed(string $variantName): void
    {
        $response = $this->client->getLastResponse();

        $productVariant = $this->responseChecker->getValue($response, 'variants');
        $this->client->executeCustomRequest(Request::custom($productVariant[0], HttpRequest::METHOD_GET));

        Assert::true(
            $this->responseChecker->hasTranslation(
                $this->client->getLastResponse(),
                'en_US',
                'name',
                $variantName
            )
        );
    }

    private function hasProductWithPriceInChannel(array $products, int $price, ChannelInterface $channel, ?string $productCode = null): bool
    {
        foreach ($products as $product) {
            if ($productCode !== null && $product['code'] !== $productCode) {
                continue;
            }

            foreach ($product['variants'] as $variantIri) {
                /** @var ProductVariantInterface $variant */
                $variant = $this->iriConverter->getItemFromIri($variantIri);

                if ($variant->getChannelPricingForChannel($channel)->getPrice() === $price) {
                    return true;
                }
            }
        }

        return false;
    }

    private function hasProductWithName(array $products, string $name): bool
    {
        foreach ($products as $product) {
            foreach ($product['translations'] as $translation) {
                if ($translation['name'] === $name) {
                    return true;
                }
            }
        }

        return false;
    }
}
