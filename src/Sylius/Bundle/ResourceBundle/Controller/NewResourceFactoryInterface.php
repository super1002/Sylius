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

namespace Sylius\Bundle\ResourceBundle\Controller;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface NewResourceFactoryInterface
{
    /**
     * @param RequestConfiguration $requestConfiguration
     * @param FactoryInterface $factory
     *
     * @return ResourceInterface
     */
    public function create(RequestConfiguration $requestConfiguration, FactoryInterface $factory): ResourceInterface;
}
