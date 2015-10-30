<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\User\Security;

use Sylius\Component\User\Model\CredentialingInterface;
use Sylius\Component\User\Model\UserInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface UserPasswordEncoderInterface
{
    /**
     * Encodes the user plain password.
     *
     * @param CredentialingInterface $user
     *
     * @return string The encoded password
     */
    public function encode(CredentialingInterface $user);
}
