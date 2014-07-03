<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle\Model;

use FOS\OAuthServerBundle\Model\ClientInterface as BaseClientInterface;

/**
 * API client interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ClientInterface extends BaseClientInterface
{
}
