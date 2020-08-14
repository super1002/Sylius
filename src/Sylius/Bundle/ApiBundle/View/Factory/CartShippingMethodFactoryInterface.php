<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\View\Factory;

use Sylius\Bundle\ApiBundle\View\CartShippingMethodInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

interface CartShippingMethodFactoryInterface
{
    public function create(
        string $code,
        ShippingMethodInterface $shippingMethod,
        int $cost
    ): CartShippingMethodInterface;
}
