<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Sylius\Bundle\PayumBundle\Model\PaymentMethodInterface as BasePaymentMethodInterface;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface PaymentMethodInterface extends BasePaymentMethodInterface, ChannelsAwareInterface
{
}
