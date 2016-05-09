<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\PromotionCoupon;

use Sylius\Behat\Page\PageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface GeneratePageInterface extends PageInterface
{
    /**
     * @param string $message
     *
     * @return bool
     */
    public function checkAmountValidation($message);

    public function  generate();

    /**
     * @param int $amount
     */
    public function specifyAmount($amount);

    /**
     * @param int $limit
     */
    public function setUsageLimit($limit);

    /**
     * @param \DateTime $date
     */
    public function setExpiresAt(\DateTime $date);
}
