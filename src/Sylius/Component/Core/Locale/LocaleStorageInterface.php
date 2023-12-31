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

namespace Sylius\Component\Core\Locale;

use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;

interface LocaleStorageInterface
{
    public function set(ChannelInterface $channel, string $localeCode): void;

    /**
     * @throws ChannelNotFoundException
     */
    public function get(ChannelInterface $channel): string;
}
