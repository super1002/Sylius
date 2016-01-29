<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Shipping\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class FlexibleRateCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Shipping\Calculator\FlexibleRateCalculator');
    }

    function it_should_implement_Sylius_shipping_calculator_interface()
    {
        $this->shouldImplement(CalculatorInterface::class);
    }

    function it_returns_flexible_rate_type()
    {
        $this->getType()->shouldReturn('flexible_rate');
    }

    function it_should_calculate_the_first_item_cost_if_shipment_has_only_one_item(ShipmentInterface $shipment)
    {
        $configuration = [
            'first_item_cost' => 1000,
            'additional_item_cost' => 200,
            'additional_item_limit' => 0,
        ];

        $shipment->getShippingItemCount()->willReturn(1);

        $this->calculate($shipment, $configuration)->shouldReturn(1000);
    }

    function it_should_calculate_the_first_and_every_additional_item_cost_when_shipment_has_more_items(
        ShipmentInterface $shipment
    ) {
        $configuration = [
            'first_item_cost' => 1500,
            'additional_item_cost' => 300,
            'additional_item_limit' => 0,
        ];

        $shipment->getShippingItemCount()->willReturn(5);

        $this->calculate($shipment, $configuration)->shouldReturn(2700);
    }

    function it_should_calculate_the_first_and_every_additional_item_cost_taking_limit_into_account(ShipmentInterface $shipment)
    {
        $configuration = [
            'first_item_cost' => 1500,
            'additional_item_cost' => 300,
            'additional_item_limit' => 3,
        ];

        $shipment->getShippingItemCount()->willReturn(8);

        $this->calculate($shipment, $configuration)->shouldReturn(2400);
    }

    function it_should_calculate_the_first_and_every_additional_item_cost_when_the_limit_is_equal_to_additional_items_number(ShipmentInterface $shipment)
    {
        $configuration = [
            'first_item_cost' => 1000,
            'additional_item_cost' => 200,
            'additional_item_limit' => 3,
        ];

        $shipment->getShippingItemCount()->willReturn(4);

        $this->calculate($shipment, $configuration)->shouldReturn(1600);
    }

    function its_calculated_value_should_be_an_integer(ShipmentInterface $shipment)
    {
        $configuration = [
            'first_item_cost' => 1090,
            'additional_item_cost' => 200,
            'additional_item_limit' => 3,
        ];

        $shipment->getShippingItemCount()->willReturn(6);

        $this->calculate($shipment, $configuration)->shouldBeInteger();
    }
}
