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

namespace Sylius\Behat\Page\Admin\ProductAttribute;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $code
     */
    public function specifyCode($code);

    /**
     * @param string $name
     * @param string $language
     */
    public function nameIt($name, $language);

    /**
     * @return bool
     */
    public function isTypeDisabled();

    public function addAttributeValue(string $value, string $localeCode): void;

    public function specifyMinValue(int $min): void;

    public function specifyMaxValue(int $max): void;

    public function checkMultiple(): void;

    /**
     * @throws ElementNotFoundException
     */
    public function getValidationErrors(): string;
}
