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

namespace spec\Sylius\Component\Core\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Factory\CustomerFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CustomerFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $baseCustomerFactory): void
    {
        $this->beConstructedWith($baseCustomerFactory);
    }

    function it_implements_customer_factory_interface(): void
    {
        $this->shouldImplement(CustomerFactoryInterface::class);
    }

    function it_is_a_resource_factory(): void
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_creates_a_new_customer(FactoryInterface $baseCustomerFactory, CustomerInterface $customer): void
    {
        $baseCustomerFactory->createNew()->willReturn($customer);

        $this->createNew()->shouldReturn($customer);
    }

    function it_creates_a_new_customer_after_checkout(
        FactoryInterface $baseCustomerFactory,
        CustomerInterface $guest,
        AddressInterface $address,
        CustomerInterface $customer
    ): void {
        $guest->getEmail()->willReturn('johndoe@example.com');
        $address->getFirstName()->willReturn('John');
        $address->getLastName()->willReturn('Doe');
        $address->getPhoneNumber()->willReturn('666777888');

        $baseCustomerFactory->createNew()->willReturn($customer);

        $customer->setEmail('johndoe@example.com')->shouldBeCalled();
        $customer->setFirstName('John')->shouldBeCalled();
        $customer->setLastName('Doe')->shouldBeCalled();
        $customer->setPhoneNumber('666777888')->shouldBeCalled();

        $this->createAfterCheckout($guest, $address)->shouldReturn($customer);
    }
}
