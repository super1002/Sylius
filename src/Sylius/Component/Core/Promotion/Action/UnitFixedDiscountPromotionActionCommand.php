<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Action;

use Sylius\Bundle\PromotionBundle\Form\Type\Action\UnitFixedDiscountConfigurationType;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Promotion\Filter\FilterInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class UnitFixedDiscountPromotionActionCommand extends UnitDiscountPromotionActionCommand
{
    const TYPE = 'unit_fixed_discount';

    /**
     * @var FilterInterface
     */
    private $priceRangeFilter;

    /**
     * @var FilterInterface
     */
    private $taxonFilter;

    /**
     * @var FilterInterface
     */
    private $productFilter;

    /**
     * @param FactoryInterface $adjustmentFactory
     * @param FilterInterface $priceRangeFilter
     * @param FilterInterface $taxonFilter
     * @param FilterInterface $productFilter
     */
    public function __construct(
        FactoryInterface $adjustmentFactory,
        FilterInterface $priceRangeFilter,
        FilterInterface $taxonFilter,
        FilterInterface $productFilter
    ) {
        parent::__construct($adjustmentFactory);

        $this->priceRangeFilter = $priceRangeFilter;
        $this->taxonFilter = $taxonFilter;
        $this->productFilter = $productFilter;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        $channelCode = $subject->getChannel()->getCode();
        if (!isset($configuration[$channelCode])) {
            return;
        }

        $amount = $configuration[$channelCode]['amount'];

        if (0 === $amount) {
            return;
        }

        $filteredItems = $this->priceRangeFilter->filter($subject->getItems()->toArray(), $configuration[$channelCode]);
        $filteredItems = $this->taxonFilter->filter($filteredItems, $configuration[$channelCode]);
        $filteredItems = $this->productFilter->filter($filteredItems, $configuration[$channelCode]);

        foreach ($filteredItems as $item) {
            $this->setUnitsAdjustments($item, $amount, $promotion);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return UnitFixedDiscountConfigurationType::class;
    }

    /**
     * @param OrderItemInterface $item
     * @param int $amount
     * @param PromotionInterface $promotion
     */
    private function setUnitsAdjustments(OrderItemInterface $item, $amount, PromotionInterface $promotion)
    {
        foreach ($item->getUnits() as $unit) {
            $this->addAdjustmentToUnit(
                $unit,
                min($unit->getTotal(), $amount),
                $promotion
            );
        }
    }
}
