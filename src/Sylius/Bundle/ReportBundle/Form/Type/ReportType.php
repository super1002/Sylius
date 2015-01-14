<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReportBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Report\Model\ReportInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Report form type.
 * 
 * @author Łukasz Chruściel <lchrusciel@gmail.com>
 * @author Mateusz Zalewski <zaleslaw@gmail.com>
 */
class ReportType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.report.name',
                'required' => true
            ))
            ->add('description', 'textarea', array(
                'label'    => 'sylius.form.report.description',
                'required' => false,
            ))
            ->add('dataFetcher', 'sylius_data_fetcher_choice', array(
                'label'    => 'sylius.form.report.data_fetcher',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_report';
    }
}
