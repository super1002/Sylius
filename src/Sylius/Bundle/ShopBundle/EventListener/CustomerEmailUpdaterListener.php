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

namespace Sylius\Bundle\ShopBundle\EventListener;

use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopSection;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Webmozart\Assert\Assert;

final class CustomerEmailUpdaterListener
{
    /** @var GeneratorInterface */
    private $tokenGenerator;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var SessionInterface */
    private $session;

    /** @var SectionProviderInterface */
    private $uriBasedSectionContext;

    public function __construct(
        GeneratorInterface $tokenGenerator,
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher,
        SessionInterface $session,
        SectionProviderInterface $uriBasedSectionContext
    ) {
        $this->tokenGenerator = $tokenGenerator;
        $this->channelContext = $channelContext;
        $this->eventDispatcher = $eventDispatcher;
        $this->session = $session;
        $this->uriBasedSectionContext = $uriBasedSectionContext;
    }

    public function eraseVerification(GenericEvent $event): void
    {
        if (!$this->uriBasedSectionContext->getSection() instanceof ShopSection) {
            return;
        }

        $customer = $event->getSubject();

        /** @var CustomerInterface $customer */
        Assert::isInstanceOf($customer, CustomerInterface::class);

        /** @var ShopUserInterface|null $user */
        $user = $customer->getUser();
        Assert::isInstanceOf($user, ShopUserInterface::class);

        if ($customer->getEmail() !== $user->getUsername()) {
            $user->setVerifiedAt(null);

            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            if ($channel->isAccountVerificationRequired()) {
                $token = $this->tokenGenerator->generate();
                $user->setEmailVerificationToken($token);

                $user->setEnabled(false);
            }
        }
    }

    public function sendVerificationEmail(GenericEvent $event): void
    {
        if (!$this->uriBasedSectionContext->getSection() instanceof ShopSection) {
            return;
        }

        $customer = $event->getSubject();

        /** @var CustomerInterface $customer */
        Assert::isInstanceOf($customer, CustomerInterface::class);

        /** @var ShopUserInterface $user */
        $user = $customer->getUser();
        Assert::isInstanceOf($user, ShopUserInterface::class);

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        if (!$channel->isAccountVerificationRequired()) {
            return;
        }

        if (!$user->isEnabled() && !$user->isVerified() && null !== $user->getEmailVerificationToken()) {
            $this->eventDispatcher->dispatch(UserEvents::REQUEST_VERIFICATION_TOKEN, new GenericEvent($user));

            /** @var FlashBagInterface $flashBag */
            $flashBag = $this->session->getBag('flashes');
            $flashBag->add('success', 'sylius.user.verify_email_request');
        }
    }
}
