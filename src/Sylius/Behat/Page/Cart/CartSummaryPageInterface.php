<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Cart;

use Sylius\Behat\Page\PageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface CartSummaryPageInterface extends PageInterface
{
    /**
     * @return string
     */
    public function getGrandTotal();

    /**
     * @return string
     */
    public function getTaxTotal();

    /**
     * @return string
     */
    public function getShippingTotal();

    /**
     * @return string
     */
    public function getPromotionTotal();

    /**
     * @param string $productName
     */
    public function removeProduct($productName);

    /**
     * @param string $productName
     * @param int $quantity
     */
    public function changeQuantity($productName, $quantity);
}
