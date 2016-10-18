<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\MoneyBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class MoneyTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('PLN');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MoneyType::class);
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_has_money_type_as_parent()
    {
        $this->getParent()->shouldReturn('money');
    }

    function it_defines_assigned_currency_and_sets_divisor_to_100(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'currency' => 'PLN',
                'divisor' => 100, ]
            )
            ->shouldBeCalled()
        ;

        $this->configureOptions($resolver);
    }
}
