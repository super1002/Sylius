<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Currency;

use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface CurrencyStorageInterface
{
    /**
     * @param ChannelInterface $channel
     * @param string $currencyCode
     */
    public function set(ChannelInterface $channel, $currencyCode);

    /**
     * @param ChannelInterface $channel
     *
     * @return string Currency code
     *
     * @throws ChannelNotFoundException
     */
    public function get(ChannelInterface $channel);
}
