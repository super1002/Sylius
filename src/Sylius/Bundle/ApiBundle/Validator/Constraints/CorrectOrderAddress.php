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

final class CorrectOrderAddress extends Constraint
{
    /** @var string */
    public $countryCodeNotExistMessage = 'sylius.country.not_exist';

    /** @var string */
    public $addressWithoutCountryCodeCanNotExistMessage = 'sylius.address.without_country';

    public function validatedBy(): string
    {
        return 'sylius_api_validator_correct_order_address';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
