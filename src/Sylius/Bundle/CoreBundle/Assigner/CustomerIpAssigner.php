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

namespace Sylius\Bundle\CoreBundle\Assigner;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;

final class CustomerIpAssigner implements IpAssignerInterface
{
    public function assign(OrderInterface $order, Request $request): void
    {
        $order->setCustomerIp($request->getClientIp());
    }
}
