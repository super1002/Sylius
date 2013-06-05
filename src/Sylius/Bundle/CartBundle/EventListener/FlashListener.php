<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\EventListener;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

use Sylius\Bundle\CartBundle\SyliusCartEvents;

/**
 * Flash message listener.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class FlashListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    public $messages;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var Translator
     */
    private $translator;

    public function setMessages()
    {
        $this->messages = array(
            SyliusCartEvents::CART_SAVE_COMPLETED    => $this->translator->trans('sylius.cart.cart_save_completed'),
            SyliusCartEvents::CART_CLEAR_COMPLETED   => $this->translator->trans('sylius.cart.cart_clear_completed'),

            SyliusCartEvents::ITEM_ADD_COMPLETED     => $this->translator->trans('sylius.cart.item_add_completed'),
            SyliusCartEvents::ITEM_REMOVE_COMPLETED  => $this->translator->trans('sylius.cart.item_remove_completed'),

            SyliusCartEvents::ITEM_ADD_ERROR         => $this->translator->trans('sylius.cart.item_add_error'),
            SyliusCartEvents::ITEM_REMOVE_ERROR      => $this->translator->trans('sylius.cart.item_remove_error')
        );

        return $this;
    }

    /**
     * @param SessionInterface $session
     * @param Translator $translator
     */
    public function __construct(SessionInterface $session, Translator $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
        $this->setMessages();
    }

    public static function getSubscribedEvents()
    {
        return array(
            SyliusCartEvents::CART_CLEAR_COMPLETED  => 'addSuccessFlash',
            SyliusCartEvents::CART_SAVE_COMPLETED   => 'addSuccessFlash',

            SyliusCartEvents::ITEM_ADD_COMPLETED    => 'addSuccessFlash',
            SyliusCartEvents::ITEM_REMOVE_COMPLETED => 'addSuccessFlash',

            SyliusCartEvents::ITEM_ADD_ERROR        => 'addErrorFlash',
            SyliusCartEvents::ITEM_REMOVE_ERROR     => 'addErrorFlash',
        );
    }

    public function addErrorFlash(Event $event)
    {
        $this->session->getFlashBag()->add('error', $event->getMessage() ?: $this->messages[$event->getName()]);
    }

    public function addSuccessFlash(Event $event)
    {
        $this->session->getFlashBag()->add('success', $event->getMessage() ?: $this->messages[$event->getName()]);
    }
}
