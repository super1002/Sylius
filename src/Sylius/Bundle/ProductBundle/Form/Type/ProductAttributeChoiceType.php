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

namespace Sylius\Bundle\ProductBundle\Form\Type;

use Sylius\Bundle\AttributeBundle\Form\Type\AttributeChoiceType;

final class ProductAttributeChoiceType extends AttributeChoiceType
{
    public function getBlockPrefix(): string
    {
        return 'sylius_product_attribute_choice';
    }
}
