<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CustomerBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomerType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('group', CustomerGroupChoiceType::class, [
                'required' => false,
            ])
        ;
    }

    public function getParent(): string
    {
        return CustomerProfileType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_customer';
    }
}
