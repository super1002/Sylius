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

namespace Sylius\Bundle\ResourceBundle\Grid\View;

use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Resource\Metadata\MetadataInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ResourceGridViewFactoryInterface
{
    /**
     * @param Grid $grid
     * @param Parameters $parameters
     * @param MetadataInterface $metadata
     * @param RequestConfiguration $requestConfiguration
     *
     * @return ResourceGridView
     */
    public function create(
        Grid $grid,
        Parameters $parameters,
        MetadataInterface $metadata,
        RequestConfiguration $requestConfiguration
    ): ResourceGridView;
}
