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

namespace Sylius\Behat\Page\Admin\ShippingCategory;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInteface;

interface CreatePageInterface extends BaseCreatePageInteface
{
    public function specifyCode(string $code): void;

    public function nameIt(string $name): void;

    public function specifyDescription(string $description): void;
}
