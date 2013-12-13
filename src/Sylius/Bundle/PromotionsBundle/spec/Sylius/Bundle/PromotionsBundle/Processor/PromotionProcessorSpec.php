<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionsBundle\Processor;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionProcessorSpec extends ObjectBehavior
{
    /**
     * @param \Sylius\Bundle\PromotionsBundle\Repository\PromotionRepositoryInterface      $repository
     * @param \Sylius\Bundle\PromotionsBundle\Checker\PromotionEligibilityCheckerInterface $checker
     * @param \Sylius\Bundle\PromotionsBundle\Action\PromotionApplicatorInterface          $applicator
     */
    function let($repository, $checker, $applicator)
    {
        $this->beConstructedWith($repository, $checker, $applicator);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Processor\PromotionProcessor');
    }

    function it_should_be_Sylius_promotion_processor()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Processor\PromotionProcessorInterface');
    }

    function it_should_not_apply_promotions_that_are_not_eligible($repository, $checker, $applicator, PromotionSubjectInterface $subject, PromotionInterface $promotion)
    {
        $repository->findActive()->shouldBeCalled()->willReturn(array($promotion));
        $checker->isEligible($subject, $promotion)->shouldBeCalled()->willReturn(false);
        $applicator->apply($subject, $promotion)->shouldNotBeCalled();
        $applicator->revert($subject, $promotion)->shouldNotBeCalled();

        $this->process($subject);
    }

    function it_should_apply_promotions_that_are_eligible($repository, $checker, $applicator, PromotionSubjectInterface $subject, PromotionInterface $promotion)
    {
        $repository->findActive()->shouldBeCalled()->willReturn(array($promotion));
        $checker->isEligible($subject, $promotion)->shouldBeCalled()->willReturn(true);
        $applicator->apply($subject, $promotion)->shouldBeCalled();
        $applicator->revert($subject, $promotion)->shouldNotBeCalled();

        $this->process($subject);
    }

    function it_should_revert_promotions_that_are_not_eligible_anymore($repository, $checker, $applicator, PromotionSubjectInterface $subject, PromotionInterface $promotion)
    {
        $promotion->hasSubject($subject)->shouldBeCalled()->willReturn(true);

        $repository->findActive()->shouldBeCalled()->willReturn(array($promotion));
        $checker->isEligible($subject, $promotion)->shouldBeCalled()->willReturn(false);

        $applicator->apply($subject, $promotion)->shouldNotBeCalled();
        $applicator->revert($subject, $promotion)->shouldBeCalled();

        $this->process($subject);
    }
}
