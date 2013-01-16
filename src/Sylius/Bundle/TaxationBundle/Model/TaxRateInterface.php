<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxationBundle\Model;

/**
 * Tax rate interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
interface TaxRateInterface
{
    /**
     * Get identifier.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get category.
     *
     * @return TaxCategoryInterface
     */
    public function getCategory();

    /**
     * Set category.
     *
     * @param null|TaxCategoryInterface $category
     */
    public function setCategory(TaxCategoryInterface $category = null);

    /**
     * Get name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get tax amount.
     *
     * @return float
     */
    public function getAmount();

    /**
     * Set tax amount.
     *
     * @param float $amount
     */
    public function setAmount($amount);

    /**
     * Is included in price?
     *
     * @return Boolean
     */
    public function isIncludedInPrice();

    /**
     * Set as included in price or not.
     *
     * @param Boolean $includedInPrice
     */
    public function setIncludedInPrice($includedInPrice);

    /**
     * Get calculator name.
     *
     * @return string
     */
    public function getCalculator();

    /**
     * Set calculator name.
     *
     * @param string $calculator
     */
    public function setCalculator($calculator);

    /**
     * Get creation time.
     *
     * @return DateTime
     */
    public function getCreatedAt();

    /**
     * Get last update time.
     *
     * @return DateTime
     */
    public function getUpdatedAt();
}
