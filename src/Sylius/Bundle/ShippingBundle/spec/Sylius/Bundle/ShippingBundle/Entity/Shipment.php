<?php

namespace spec\Sylius\Bundle\ShippingBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Shipment mapped superclass spec.
 *
 * @author Pawęł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Shipment extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Entity\Shipment');
    }

    function it_should_be_a_Sylius_shipment()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Model\ShipmentInterface');
    }

    function it_should_extend_Sylius_shipment_model()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Model\Shipment');
    }
}
