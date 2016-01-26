<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\OrderProcessing;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\OrderProcessing\OrderShipmentTaxesApplicatorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderShipmentTaxesApplicatorSpec extends ObjectBehavior
{
    function let(
        CalculatorInterface $calculator,
        FactoryInterface $adjustmentsFactory
    ) {
        $this->beConstructedWith($calculator, $adjustmentsFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\OrderProcessing\OrderShipmentTaxesApplicator');
    }

    function it_implements_order_shipment_taxes_applicator_interface()
    {
        $this->shouldImplement(OrderShipmentTaxesApplicatorInterface::class);
    }

    function it_applies_shipment_taxes_on_order_based_on_shipment_adjustments_and_rate(
        $adjustmentsFactory,
        $calculator,
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $shippingTaxAdjustment,
        Collection $shippingAdjustments,
        OrderInterface $order,
        TaxRateInterface $taxRate
    ) {
        $taxRate->getAmountAsPercentage()->willReturn(10);
        $taxRate->getName()->willReturn('Simple tax');
        $taxRate->isIncludedInPrice()->willReturn(false);

        $order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->willReturn($shippingAdjustments);
        $shippingAdjustments->isEmpty()->willReturn(false);
        $shippingAdjustments->last()->willReturn($shippingAdjustment);
        $shippingAdjustment->getAmount()->willReturn(1000);

        $calculator->calculate(1000, $taxRate)->willReturn(100);

        $adjustmentsFactory->createNew()->willReturn($shippingTaxAdjustment);
        $shippingTaxAdjustment->setDescription('Simple tax (10%)')->shouldBeCalled();
        $shippingTaxAdjustment->setAmount(100)->shouldBeCalled();
        $shippingTaxAdjustment->setType(AdjustmentInterface::TAX_ADJUSTMENT)->shouldBeCalled();
        $shippingTaxAdjustment->setNeutral(false)->shouldBeCalled();

        $order->addAdjustment($shippingTaxAdjustment)->shouldBeCalled();

        $this->apply($order, $taxRate);
    }

    function it_does_nothing_if_order_has_no_shipping_adjustments(Collection $shippingAdjustments, OrderInterface $order, TaxRateInterface $taxRate)
    {
        $order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->willReturn($shippingAdjustments);
        $shippingAdjustments->isEmpty()->willReturn(true);

        $shippingAdjustments->last()->shouldNotBeCalled();

        $this->apply($order, $taxRate);
    }
}
