<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Taxation\Processor\OrderTaxesProcessorInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessorInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class OrderRecalculator implements OrderRecalculatorInterface
{
    /**
     * @var OrderTaxesProcessorInterface
     */
    private $taxesProcessor;

    /**
     * @var PricesRecalculatorInterface
     */
    private $pricesRecalculator;

    /**
     * @var PromotionProcessorInterface
     */
    private $promotionProcessor;

    /**
     * @var ShippingChargesProcessorInterface
     */
    private $shippingChargesProcessor;

    /**
     * @param OrderTaxesProcessorInterface $taxesProcessor
     * @param PricesRecalculatorInterface $pricesRecalculator
     * @param PromotionProcessorInterface $promotionProcessor
     * @param ShippingChargesProcessorInterface $shippingChargesProcessor
     */
    public function __construct(
        OrderTaxesProcessorInterface $taxesProcessor,
        PricesRecalculatorInterface $pricesRecalculator,
        PromotionProcessorInterface $promotionProcessor,
        ShippingChargesProcessorInterface $shippingChargesProcessor
    ) {
        $this->taxesProcessor = $taxesProcessor;
        $this->pricesRecalculator = $pricesRecalculator;
        $this->promotionProcessor = $promotionProcessor;
        $this->shippingChargesProcessor = $shippingChargesProcessor;
    }

    /**
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function recalculate(OrderInterface $order)
    {
        $this->pricesRecalculator->recalculate($order);
        $this->shippingChargesProcessor->applyShippingCharges($order);
        $this->promotionProcessor->process($order);
        $this->taxesProcessor->process($order);
    }
}
