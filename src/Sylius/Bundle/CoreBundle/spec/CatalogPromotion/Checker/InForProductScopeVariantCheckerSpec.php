<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\VariantInScopeCheckerInterface;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class InForProductScopeVariantCheckerSpec extends ObjectBehavior
{
    function it_implements_catalog_promotion_price_calculator_interface(): void
    {
        $this->shouldImplement(VariantInScopeCheckerInterface::class);
    }

    public function it_returns_true_if_product_variant_is_in_scope_configuration(
        CatalogPromotionScopeInterface $scope,
        ProductVariantInterface $variant,
        ProductInterface $product,
    ): void {
        $scope->getConfiguration()->willReturn(['products' => ['FIRST_PRODUCT', 'SECOND_PRODUCT']]);

        $variant->getProduct()->willReturn($product);
        $product->getCode()->willReturn('FIRST_PRODUCT');

        $this->inScope($scope, $variant)->shouldReturn(true);
    }

    public function it_returns_false_if_product_variant_is_not_in_scope_configuration(
        CatalogPromotionScopeInterface $scope,
        ProductVariantInterface $variant,
        ProductInterface $product,
    ): void {
        $scope->getConfiguration()->willReturn(['products' => ['FIRST_PRODUCT', 'SECOND_PRODUCT']]);

        $variant->getProduct()->willReturn($product);
        $product->getCode()->willReturn('ANOTHER_PRODUCT');

        $this->inScope($scope, $variant)->shouldReturn(false);
    }

    public function it_throws_exception_if_scope_does_not_contains_product_configuration(
        CatalogPromotionScopeInterface $scope,
        ProductVariantInterface $variant,
    ): void {
        $scope->getConfiguration()->willReturn(['FOO' => ['BOO']]);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('inScope', [$scope, $variant])
        ;
    }
}
