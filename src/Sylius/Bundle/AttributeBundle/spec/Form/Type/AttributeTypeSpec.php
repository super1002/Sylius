<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AttributeBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AttributeBundle\Form\EventListener\BuildAttributeFormChoicesListener;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Leszek Prabucki <leszek.prabucki@gmail.pl>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class AttributeTypeSpec extends ObjectBehavior
{
    function let(FormBuilder $builder, FormFactoryInterface $formFactory, ServiceRegistryInterface $attributeTypeRegistry)
    {
        $this->beConstructedWith('Attribute', array('sylius'), 'server', $attributeTypeRegistry);

        $builder->getFormFactory()->willReturn($formFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AttributeBundle\Form\Type\AttributeType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->addEventSubscriber(Argument::type('Sylius\Bundle\AttributeBundle\Form\EventSubscriber\BuildAttributeFormSubscriber'))
            ->willReturn($builder)
        ;

        $builder
            ->add('translations', 'a2lix_translationsForms', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('code', 'text', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('type', 'sylius_attribute_type_choice', array(
                'label'    => 'sylius.form.attribute.type',
                'disabled' => true,
            ))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    function it_defines_assigned_data_class(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Attribute', 'validation_groups' => array('sylius')))->shouldBeCalled();

        $this->configureOptions($resolver);
    }

    function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_server_attribute');
    }
}
