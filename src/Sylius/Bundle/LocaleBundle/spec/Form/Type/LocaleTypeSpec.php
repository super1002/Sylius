<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\LocaleBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\LocaleBundle\Form\Type\LocaleType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @mixin LocaleType
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class LocaleTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(\Locale::class, ['sylius']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LocaleType::class);
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_should_build_form_with_proper_fields(FormBuilderInterface $builder)
    {
        $builder
            ->add('code', 'locale', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('enabled', 'checkbox', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, []);
    }

    function it_defines_assigned_data_class_and_validation_groups(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => \Locale::class,
                'validation_groups' => ['sylius'],
            ])
            ->shouldBeCalled()
        ;

        $this->configureOptions($resolver);
    }

    function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_locale');
    }
}
