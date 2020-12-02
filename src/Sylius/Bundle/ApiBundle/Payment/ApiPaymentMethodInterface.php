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

namespace Sylius\Bundle\ApiBundle\Payment;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

interface ApiPaymentMethodInterface
{
    public function supports(PaymentMethodInterface $paymentMethod): bool;

    public function provideConfiguration(PaymentInterface $payment): array;
}
