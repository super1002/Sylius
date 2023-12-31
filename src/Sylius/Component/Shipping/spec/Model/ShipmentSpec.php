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

namespace spec\Sylius\Component\Shipping\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShipmentUnitInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

final class ShipmentSpec extends ObjectBehavior
{
    function it_implements_shipment_interface(): void
    {
        $this->shouldImplement(ShipmentInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_ready_state_by_default(): void
    {
        $this->getState()->shouldReturn(ShipmentInterface::STATE_CART);
    }

    function its_state_is_mutable(): void
    {
        $this->setState(ShipmentInterface::STATE_SHIPPED);
        $this->getState()->shouldReturn(ShipmentInterface::STATE_SHIPPED);
    }

    function it_has_no_shipping_method_by_default(): void
    {
        $this->getMethod()->shouldReturn(null);
    }

    function its_shipping_method_is_mutable(ShippingMethodInterface $shippingMethod): void
    {
        $this->setMethod($shippingMethod);
        $this->getMethod()->shouldReturn($shippingMethod);
    }

    function it_initializes_units_collection_by_default(): void
    {
        $this->getUnits()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function it_adds_units(ShipmentUnitInterface $shipmentUnit): void
    {
        $this->hasUnit($shipmentUnit)->shouldReturn(false);

        $shipmentUnit->setShipment($this)->shouldBeCalled();
        $this->addUnit($shipmentUnit);

        $this->hasUnit($shipmentUnit)->shouldReturn(true);
    }

    function it_removes_unit(ShipmentUnitInterface $shipmentUnit): void
    {
        $this->hasUnit($shipmentUnit)->shouldReturn(false);

        $shipmentUnit->setShipment($this)->shouldBeCalled();
        $this->addUnit($shipmentUnit);

        $shipmentUnit->setShipment(null)->shouldBeCalled();
        $this->removeUnit($shipmentUnit);

        $this->hasUnit($shipmentUnit)->shouldReturn(false);
    }

    function it_has_no_tracking_code_by_default(): void
    {
        $this->getTracking()->shouldReturn(null);
    }

    function its_tracking_code_is_mutable(): void
    {
        $this->setTracking('5346172074');
        $this->getTracking()->shouldReturn('5346172074');
    }

    function it_is_not_tracked_by_default(): void
    {
        $this->shouldNotBeTracked();
    }

    function it_is_tracked_only_if_tracking_code_is_defined(): void
    {
        $this->shouldNotBeTracked();
        $this->setTracking('5346172074');
        $this->shouldBeTracked();
        $this->setTracking(null);
        $this->shouldNotBeTracked();
    }

    function it_initializes_creation_date_by_default(): void
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function its_creation_date_is_mutable(): void
    {
        $date = new \DateTime();

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default(): void
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable(): void
    {
        $date = new \DateTime();

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
