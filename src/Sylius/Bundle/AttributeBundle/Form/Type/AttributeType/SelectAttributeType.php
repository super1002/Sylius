<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\Type\AttributeType;

use Sylius\Bundle\AttributeBundle\Form\DataTransformer\StringToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Laurent Paganin-Gioanni <l.paganin@algo-factory.com>
 */
class SelectAttributeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['configuration']['multiple']) {
            $transformer = new StringToArrayTransformer();
            $builder->addModelTransformer($transformer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('configuration')
            ->setDefault('placeholder', 'sylius.form.attribute_type_configuration.select.choose')
            ->setNormalizer('choices', function(Options $options){
                $choices = array_flip($options['configuration']['options']);
                ksort($choices);
                return $choices;
            })
            ->setNormalizer('multiple', function(Options $options){
                return $options['configuration']['multiple'];
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_attribute_type_select';
    }
}
