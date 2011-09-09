<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;

/**
 * Address form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class AddressFormType extends AbstractType
{
    /**
     * Data class.
     * 
     * @var string
     */
    protected $dataClass;
    
    /**
     * Constructor.
     * 
     * @param string $dataClass
     */
    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('company', 'text', array('required' => false))
            ->add('name', 'text')
            ->add('surname', 'text')
            ->add('street', 'text')
            ->add('postcode', 'text')
            ->add('city', 'text')
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => $this->dataClass
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_addressing_address';
    }
}
