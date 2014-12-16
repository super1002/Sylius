<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class ActionTypeSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $actionRegistry)
    {
        $this->beConstructedWith('Action', array('sylius'), $actionRegistry);
    }

    function it_is_initializabled()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionBundle\Form\Type\ActionType');
    }

    function it_is_configuration_form_type()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionBundle\Form\Type\Core\AbstractConfigurationType');
    }

    function it_builds_form(
        FormBuilder $builder,
        FormFactoryInterface $factory
    ) {
        $builder
            ->add('type', 'sylius_promotion_action_choice', Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder->getFormFactory()->willReturn($factory);
        $builder->addEventSubscriber(
            Argument::type('Sylius\Bundle\PromotionBundle\Form\EventListener\BuildActionFormSubscriber')
        )->shouldBeCalled();

        $this->buildForm($builder, array(
            'configuration_type' => 'configuration_form_type'
        ));
    }

    function it_should_define_assigned_data_class(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Action',
            'validation_groups' => array('sylius'),
        ))->shouldBeCalled();

        $resolver->setOptional(array('configuration_type'))->shouldBeCalled();
        $resolver->setDefaults(array('configuration_type' => ActionInterface::TYPE_FIXED_DISCOUNT))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_promotion_action');
    }
}
