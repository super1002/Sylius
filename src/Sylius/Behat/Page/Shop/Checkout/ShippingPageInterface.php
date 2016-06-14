<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Checkout;

use Sylius\Behat\Page\SymfonyPageInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface ShippingPageInterface extends SymfonyPageInterface
{
    /**
     * @param string $shippingMethod
     */
    public function selectShippingMethod($shippingMethod);

    /**
     * @param string $shippingMethod
     *
     * @return bool
     */
    public function hasShippingMethod($shippingMethod);

    /**
     * @return bool
     */
    public function hasNoShippingMethodsMessage();

    public function nextStep();
}
