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

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class PasswordUpdaterListenerSpec extends ObjectBehavior
{
    function let(PasswordUpdaterInterface $passwordUpdater): void
    {
        $this->beConstructedWith($passwordUpdater);
    }

    function it_updates_password_for_customer(
        PasswordUpdaterInterface $passwordUpdater,
        GenericEvent $event,
        UserInterface $user,
        CustomerInterface $customer,
    ): void {
        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn($user);
        $user->getPlainPassword()->willReturn('password123');

        $passwordUpdater->updatePassword($user)->shouldBeCalled();

        $this->customerUpdateEvent($event);
    }

    function it_does_not_update_password_if_subject_is_not_instance_of_customer_interface(
        GenericEvent $event,
        UserInterface $user,
    ): void {
        $event->getSubject()->willReturn($user);

        $this->shouldThrow(\InvalidArgumentException::class)->during('customerUpdateEvent', [$event]);
    }

    function it_does_not_update_password_if_customer_does_not_have_user(
        PasswordUpdaterInterface $passwordUpdater,
        GenericEvent $event,
        CustomerInterface $customer,
    ): void {
        $event->getSubject()->willReturn($customer);
        $customer->getUser()->willReturn(null);

        $passwordUpdater->updatePassword(null)->shouldNotBeCalled();

        $this->customerUpdateEvent($event);
    }
}
