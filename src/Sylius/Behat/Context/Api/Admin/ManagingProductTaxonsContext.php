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

namespace Sylius\Behat\Context\Api\Admin;

use ApiPlatform\Core\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final class ManagingProductTaxonsContext implements Context
{
    public function __construct(private ApiClientInterface $client, private IriConverterInterface $iriConverter)
    {
    }

    /**
     * @When I change that the :product product belongs to the :taxon taxon
     */
    public function iChangeThatTheProductBelongsToTheTaxon(ProductInterface $product, TaxonInterface $taxon): void
    {
        $this->client->buildUpdateRequest(Resources::PRODUCT_TAXONS, (string) $product->getProductTaxons()->current()->getId());
        $this->client->updateRequestData(['taxon' => $this->iriConverter->getIriFromItem($taxon)]);
        $this->client->update();
    }

    /**
     * @When I assign the :taxon taxon to the :product product
     * @When I (try to) add :taxon taxon to the :product product
     */
    public function iAddTaxonToTheProduct(TaxonInterface $taxon, ProductInterface $product): void
    {
        $this->client->buildCreateRequest(Resources::PRODUCT_TAXONS);
        $this->client->addRequestData('taxon', $this->iriConverter->getIriFromItem($taxon));
        $this->client->addRequestData('product', $this->iriConverter->getIriFromItem($product));
        $this->client->create();
    }

    /**
     * @When I try to assign an empty taxon to the :product product
     */
    public function iTryToAssignAnEmptyTaxonToTheProduct(ProductInterface $product): void
    {
        $this->client->buildCreateRequest(Resources::PRODUCT_TAXONS);
        $this->client->addRequestData('product', $this->iriConverter->getIriFromItem($product));
        $this->client->create();
    }

    /**
     * @When I try to assign an empty product to the :taxon taxon
     */
    public function iTryToAssignAnEmptyProductToTheTaxon(TaxonInterface $taxon): void
    {
        $this->client->buildCreateRequest(Resources::PRODUCT_TAXONS);
        $this->client->addRequestData('taxon', $this->iriConverter->getIriFromItem($taxon));
        $this->client->create();
    }

    /**
     * @Then I should be notified that specifying a :part is required
     */
    public function iShouldBeNotifiedThatSpecifyingAIsRequired(string $part): void
    {
        Assert::contains(
            $this->client->getLastResponse()->getContent(),
            sprintf('Please select a %s.', $part),
        );
    }

    /**
     * @Then I should be notified that this taxon is already assigned to this product
     */
    public function iShouldBeNotifiedThatThisTaxonIsAlreadyAssignedToThisProduct(): void
    {
        Assert::contains(
            $this->client->getLastResponse()->getContent(),
            'This taxon is already assigned to this product.',
        );
    }
}
