<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Modifier;

use Sylius\Bundle\OrderBundle\Factory\OrderItemUnitFactoryInterface;
use Sylius\Component\Order\Model\OrderItemInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderItemQuantityModifier implements OrderItemQuantityModifierInterface
{
    /**
     * @var OrderItemUnitFactoryInterface
     */
    private $orderItemUnitFactory;

    /**
     * @param OrderItemUnitFactoryInterface $orderItemUnitFactory
     */
    public function __construct(OrderItemUnitFactoryInterface $orderItemUnitFactory)
    {
        $this->orderItemUnitFactory = $orderItemUnitFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function modify(OrderItemInterface $orderItem, $targetQuantity)
    {
        $itemQuantity = $orderItem->getQuantity();
        if (0 >= $targetQuantity || $itemQuantity === $targetQuantity) {
            return;
        }

        if ($targetQuantity < $itemQuantity) {
            $this->decreaseUnitsNumber($orderItem, $itemQuantity - $targetQuantity);
        } else if ($targetQuantity > $itemQuantity) {
            $this->increaseUnitsNumber($orderItem, $targetQuantity - $itemQuantity);
        }
    }

    /**
     * @param OrderItemInterface $orderItem
     * @param int $increaseBy
     */
    private function increaseUnitsNumber(OrderItemInterface $orderItem, $increaseBy)
    {
        for ($i = 0; $i < $increaseBy; $i++) {
            $this->orderItemUnitFactory->createForItem($orderItem);
        }
    }

    /**
     * @param OrderItemInterface $orderItem
     * @param int $decreaseBy
     */
    private function decreaseUnitsNumber(OrderItemInterface $orderItem, $decreaseBy)
    {
        foreach ($orderItem->getUnits() as $unit) {
            if (0 >= $decreaseBy--) {
                break;
            }

            $orderItem->removeUnit($unit);
        }
    }
}
