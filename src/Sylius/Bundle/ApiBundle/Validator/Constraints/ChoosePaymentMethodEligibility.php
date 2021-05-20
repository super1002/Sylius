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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class ChoosePaymentMethodEligibility extends Constraint
{
    /** @var string */
    public $paymentMethodNotExistMessage = 'sylius.payment_method.not_exist';

    public function validatedBy(): string
    {
        return 'sylius_api_choose_payment_method_eligibility';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
