<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ProductBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ProductBundle\Form\EventListener\BuildVariantFormSubscriber;
use Sylius\Component\Product\Model\OptionInterface;
use Sylius\Component\Product\Model\OptionValueInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\VariantInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @mixin BuildVariantFormSubscriber
 */
final class BuildVariantFormSubscriberSpec extends ObjectBehavior
{
    function let(FormFactoryInterface $factory)
    {
        $this->beConstructedWith($factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BuildVariantFormSubscriber::class);
    }

    function it_subscribes_to_event()
    {
        static::getSubscribedEvents()->shouldReturn(
            [FormEvents::PRE_SET_DATA => 'preSetData']
        );
    }

    function it_adds_options_on_pre_set_data_event(
        FormFactoryInterface $factory,
        FormEvent $event,
        FormInterface $form,
        FormInterface $optionsForm,
        ProductInterface $variable,
        VariantInterface $variant,
        OptionValueInterface $optionValue,
        OptionInterface $options
    ) {
        $event->getForm()->shouldBeCalled()->willReturn($form);
        $event->getData()->shouldBeCalled()->willReturn($variant);

        $variant->getProduct()->shouldBeCalled()->willReturn($variable);
        $variant->getOptions()->shouldBeCalled()->willReturn([$optionValue]);
        $variable->getOptions()->shouldBeCalled()->willReturn([$options]);
        $variable->hasOptions()->shouldBeCalled()->willReturn(true);

        $factory->createNamed(
            'options',
            'sylius_product_option_value_collection',
            [$optionValue],
            [
                'options' => [$options],
                'auto_initialize' => false,
            ]
        )->shouldBeCalled()->willReturn($optionsForm);

        $form->add($optionsForm)->shouldBeCalled();

        $this->preSetData($event);
    }
}
