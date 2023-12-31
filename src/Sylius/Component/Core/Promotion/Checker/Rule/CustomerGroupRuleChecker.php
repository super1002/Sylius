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

namespace Sylius\Component\Core\Promotion\Checker\Rule;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

class CustomerGroupRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'customer_group';

    /**
     * @throws UnsupportedTypeException
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration): bool
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnsupportedTypeException($subject, OrderInterface::class);
        }

        if (null === $customer = $subject->getCustomer()) {
            return false;
        }

        if (!$customer instanceof CustomerInterface) {
            return false;
        }

        if (null === $customer->getGroup()) {
            return false;
        }

        return $configuration['group_code'] === $customer->getGroup()->getCode();
    }
}
