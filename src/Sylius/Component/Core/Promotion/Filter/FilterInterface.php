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

namespace Sylius\Component\Core\Promotion\Filter;

use Sylius\Component\Core\Model\OrderItemInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface FilterInterface
{
    /**
     * @param OrderItemInterface[] $items
     * @param array $configuration
     *
     * @return OrderItemInterface[]
     */
    public function filter(array $items, array $configuration): array;
}
