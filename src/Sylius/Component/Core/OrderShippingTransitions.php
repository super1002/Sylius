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

namespace Sylius\Component\Core;

interface OrderShippingTransitions
{
    public const GRAPH = 'sylius_order_shipping';

    public const TRANSITION_REQUEST_SHIPPING = 'request_shipping';

    public const TRANSITION_PARTIALLY_SHIP = 'partially_ship';

    public const TRANSITION_SHIP = 'ship';

    public const TRANSITION_CANCEL = 'cancel';
}
