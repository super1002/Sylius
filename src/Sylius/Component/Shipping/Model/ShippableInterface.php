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

namespace Sylius\Component\Shipping\Model;

interface ShippableInterface
{
    public function getShippingWeight(): ?float;

    public function getShippingVolume(): ?float;

    public function getShippingWidth(): ?float;

    public function getShippingHeight(): ?float;

    public function getShippingDepth(): ?float;

    public function getShippingCategory(): ?ShippingCategoryInterface;
}
