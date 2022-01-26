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

namespace spec\Sylius\Bundle\CoreBundle\Applicator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Applicator\ActionBasedDiscountApplicatorInterface;
use Sylius\Bundle\CoreBundle\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class CatalogPromotionApplicatorSpec extends ObjectBehavior
{
    function let(
        ChannelRepositoryInterface $channelRepository,
        ActionBasedDiscountApplicatorInterface $actionBasedDiscountApplicator,
    ): void {
        $this->beConstructedWith($channelRepository, $actionBasedDiscountApplicator);
    }

    function it_implements_catalog_promotion_applicator_interface(): void
    {
        $this->shouldImplement(CatalogPromotionApplicatorInterface::class);
    }

    function it_applies_percentage_discount_on_product_variant(
        ProductVariantInterface $variant,
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $catalogPromotionAction,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
        ChannelPricingInterface $firstChannelPricing,
        ChannelPricingInterface $secondChannelPricing,
        ActionBasedDiscountApplicatorInterface $actionBasedDiscountApplicator
    ): void {
        $catalogPromotion->isExclusive()->willReturn(false);
        $catalogPromotion->getActions()->willReturn(new ArrayCollection([$catalogPromotionAction->getWrappedObject()]));
        $catalogPromotion->getChannels()->willReturn(new ArrayCollection([
            $firstChannel->getWrappedObject(),
            $secondChannel->getWrappedObject(),
        ]));
        $catalogPromotionAction->getConfiguration()->willReturn(['amount' => 0.3]);

        $variant->getChannelPricingForChannel($firstChannel)->willReturn($firstChannelPricing);
        $variant->getChannelPricingForChannel($secondChannel)->willReturn($secondChannelPricing);

        $actionBasedDiscountApplicator->applyDiscountOnChannelPricing($catalogPromotion, $catalogPromotionAction, $firstChannelPricing)->shouldBeCalled();
        $actionBasedDiscountApplicator->applyDiscountOnChannelPricing($catalogPromotion, $catalogPromotionAction, $secondChannelPricing)->shouldBeCalled();

        $this->applyOnVariant($variant, $catalogPromotion);
    }
}
