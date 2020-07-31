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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\AddressOrder;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class AddressOrderHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $customerFactory,
        ObjectManager $manager,
        StateMachineFactoryInterface $stateMachineFactory
    ): void {
        $this->beConstructedWith($orderRepository, $customerFactory, $manager, $stateMachineFactory);
    }

    function it_handles_addressing_an_order_for_visitor(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $customerFactory,
        ObjectManager $manager,
        StateMachineFactoryInterface $stateMachineFactory,
        CustomerInterface $customer,
        AddressInterface $billingAddress,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ): void {
        $addressOrder = new AddressOrder('r2d2@droid.com', $billingAddress->getWrappedObject());
        $addressOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $order->getCustomer()->willReturn(null);

        $customerFactory->createNew()->willReturn($customer);
        $customer->setEmail('r2d2@droid.com')->shouldBeCalled();
        $manager->persist($customer)->shouldBeCalled();
        $order->setCustomer($customer)->shouldBeCalled();

        $order->setBillingAddress($billingAddress)->shouldBeCalled();

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('address')->willReturn(true);
        $stateMachine->apply('address')->shouldBeCalled();

        $this($addressOrder);
    }

    function it_handles_addressing_an_order_for_logged_in_shop_user(
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $customerFactory,
        ObjectManager $manager,
        StateMachineFactoryInterface $stateMachineFactory,
        CustomerInterface $customer,
        AddressInterface $billingAddress,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ): void {
        $addressOrder = new AddressOrder('r2d2@droid.com', $billingAddress->getWrappedObject());
        $addressOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $order->getCustomer()->willReturn($customer);

        $customerFactory->createNew()->shouldNotBeCalled();
        $customer->setEmail('r2d2@droid.com')->shouldNotBeCalled();
        $manager->persist($customer)->shouldNotBeCalled();
        $order->setCustomer($customer)->shouldNotBeCalled();

        $order->setBillingAddress($billingAddress)->shouldBeCalled();

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('address')->willReturn(true);
        $stateMachine->apply('address')->shouldBeCalled();

        $this($addressOrder);
    }

    function it_throws_an_exception_if_visitor_does_not_provide_an_email(
        OrderRepositoryInterface $orderRepository,
        AddressInterface $billingAddress,
        StateMachineFactoryInterface $stateMachineFactory,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ): void {
        $addressOrder = new AddressOrder(null, $billingAddress->getWrappedObject());
        $addressOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $order->getCustomer()->willReturn(null);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('address')->willReturn(true);

        $this->shouldThrow(\LogicException::class)->during('__invoke', [$addressOrder]);
    }

    function it_throws_an_exception_if_order_does_not_exist(
        OrderRepositoryInterface $orderRepository,
        AddressInterface $billingAddress
    ): void {
        $addressOrder = new AddressOrder('r2d2@droid.com', $billingAddress->getWrappedObject());
        $addressOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $this->shouldThrow(\LogicException::class)->during('__invoke', [$addressOrder]);
    }

    function it_throws_an_exception_if_order_cannot_be_addressed(
        OrderRepositoryInterface $orderRepository,
        StateMachineFactoryInterface $stateMachineFactory,
        AddressInterface $billingAddress,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ): void {
        $addressOrder = new AddressOrder('r2d2@droid.com', $billingAddress->getWrappedObject());
        $addressOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('address')->willReturn(false);

        $this->shouldThrow(\LogicException::class)->during('__invoke', [$addressOrder]);
    }
}
