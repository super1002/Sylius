<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\User;

use Sylius\Bundle\UserBundle\Form\Type\UserType as BaseUserType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class AdminUserType extends BaseUserType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('localeCode', LocaleType::class, [
                'label' => 'sylius.ui.locale',
                'placeholder' => null,
            ])
        ;
    }
}
