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

namespace Sylius\Bundle\MoneyBundle\Templating\Helper;

interface FormatMoneyHelperInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function formatAmount(int $amount, string $currencyCode, string $localeCode): string;
}
