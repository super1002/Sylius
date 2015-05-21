<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\EventListener;

use Sylius\Bundle\UserBundle\Security\UserLoginInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * User register listener.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserLoginListener
{
    /**
     * @var UserLoginInterface
     */
    protected $userLogin;

    /**
     * @var PasswordUpdaterInterface
     */
    protected $passwordUpdater;

    public function __construct(UserLoginInterface $userLogin)
    {
        $this->userLogin = $userLogin;
    }

    public function login(GenericEvent $event)
    {
        $user = $event->getSubject();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException(
                $user,
                'Sylius\Component\User\Model\UserInterface'
            );
        }

        $this->userLogin->login($user);
    }
}
