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

namespace Sylius\Component\Core\Test\Factory;

use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class TestPromotionFactory implements TestPromotionFactoryInterface
{
    public function __construct(private FactoryInterface $promotionFactory)
    {
    }

    public function create(string $name): PromotionInterface
    {
        /** @var PromotionInterface $promotion */
        $promotion = $this->promotionFactory->createNew();

        $promotion->setName($name);
        $promotion->setCode(StringInflector::nameToLowercaseCode($name));
        $promotion->setStartsAt(new \DateTime('-3 days'));
        $promotion->setEndsAt(new \DateTime('+3 days'));

        return $promotion;
    }

    public function createForChannel(string $name, ChannelInterface $channel): PromotionInterface
    {
        $promotion = $this->create($name);
        $promotion->addChannel($channel);

        return $promotion;
    }
}
