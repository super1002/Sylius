<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\PromotionRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Provider\PreQualifiedPromotionsProviderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ActiveByChannelPromotionsProviderSpec extends ObjectBehavior
{
    function let(PromotionRepository $promotionRepository)
    {
        $this->beConstructedWith($promotionRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Provider\ActiveByChannelPromotionsProvider');
    }

    function it_implements_active_promotions_provider_interface()
    {
        $this->shouldImplement(PreQualifiedPromotionsProviderInterface::class);
    }

    function it_provides_active_promotions_for_given_subject_channel(
        PromotionRepository $promotionRepository,
        ChannelInterface $channel,
        PromotionInterface $promotion1,
        PromotionInterface $promotion2,
        OrderInterface $subject
    ) {
        $subject->getChannel()->willReturn($channel);
        $promotionRepository->findActiveByChannel($channel)->willReturn([$promotion1, $promotion2]);

        $this->getPromotions($subject)->shouldReturn([$promotion1, $promotion2]);
    }
}
