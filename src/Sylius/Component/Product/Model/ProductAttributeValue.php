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

namespace Sylius\Component\Product\Model;

use Sylius\Component\Attribute\Model\AttributeValue as BaseAttributeValue;
use Webmozart\Assert\Assert;

class ProductAttributeValue extends BaseAttributeValue implements ProductAttributeValueInterface
{
    public function getProduct(): ?ProductInterface
    {
        $subject = parent::getSubject();

        /** @var ProductInterface|null $subject */
        Assert::nullOrIsInstanceOf($subject, ProductInterface::class);

        return $subject;
    }

    public function setProduct(?ProductInterface $product): void
    {
        parent::setSubject($product);
    }
}
