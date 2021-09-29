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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\CatalogPromotionRuleInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CatalogPromotionContext implements Context
{
    private ExampleFactoryInterface $catalogPromotionExampleFactory;

    private FactoryInterface $catalogPromotionRuleFactory;

    private FactoryInterface $catalogPromotionActionFactory;

    private EntityManagerInterface $entityManager;

    private ChannelRepositoryInterface $channelRepository;

    private MessageBusInterface $eventBus;

    private SharedStorageInterface $sharedStorage;

    public function __construct(
        ExampleFactoryInterface $catalogPromotionExampleFactory,
        FactoryInterface $catalogPromotionRuleFactory,
        FactoryInterface $catalogPromotionActionFactory,
        EntityManagerInterface $entityManager,
        ChannelRepositoryInterface $channelRepository,
        MessageBusInterface $eventBus,
        SharedStorageInterface $sharedStorage
    ) {
        $this->catalogPromotionExampleFactory = $catalogPromotionExampleFactory;
        $this->catalogPromotionRuleFactory = $catalogPromotionRuleFactory;
        $this->catalogPromotionActionFactory = $catalogPromotionActionFactory;
        $this->entityManager = $entityManager;
        $this->channelRepository = $channelRepository;
        $this->eventBus = $eventBus;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given there is a catalog promotion with :code code and :name name
     */
    public function thereIsACatalogPromotionWithCodeAndName(string $code, string $name): void
    {
        $this->createCatalogPromotion($name, $code);

        $this->entityManager->flush();
    }

    /**
     * @Given /^(it) is enabled$/
     */
    public function itIsEnabled(CatalogPromotionInterface $catalogPromotion): void
    {
        $catalogPromotion->setEnabled(true);

        $this->entityManager->flush();
    }

    /**
     * @Given /^(this catalog promotion) is disabled$/
     */
    public function thisCatalogPromotionIsDisabled(CatalogPromotionInterface $catalogPromotion): void
    {
        $catalogPromotion->setEnabled(false);

        $this->entityManager->flush();
    }

    /**
     * @Given there are catalog promotions named :firstName and :secondName
     */
    public function thereAreCatalogPromotionsNamed(string ...$names): void
    {
        foreach ($names as $name) {
            $this->createCatalogPromotion($name);
        }

        $this->entityManager->flush();
    }

    /**
     * @Given the catalog promotion :catalogPromotion is available in :channel
     */
    public function theCatalogPromotionIsAvailableIn(
        CatalogPromotionInterface $catalogPromotion,
        ChannelInterface $channel
    ): void {
        $catalogPromotion->addChannel($channel);

        $this->entityManager->flush();
    }

    /**
     * @Given /^(it) applies on ("[^"]+" variant)$/
     */
    public function itAppliesOnVariant(CatalogPromotionInterface $catalogPromotion, ProductVariantInterface $variant): void
    {
        /** @var CatalogPromotionRuleInterface $catalogPromotionRule */
        $catalogPromotionRule = $this->catalogPromotionRuleFactory->createNew();
        $catalogPromotionRule->setType(CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS);
        $catalogPromotionRule->setConfiguration(['variants' => [$variant->getCode()]]);

        $catalogPromotion->addRule($catalogPromotionRule);

        $this->entityManager->flush();
    }

    /**
     * @Given /^(it) reduces price by ("[^"]+")$/
     */
    public function itWillReducePrice(CatalogPromotionInterface $catalogPromotion, float $discount): void
    {
        /** @var CatalogPromotionActionInterface $catalogPromotionAction */
        $catalogPromotionAction = $this->catalogPromotionActionFactory->createNew();
        $catalogPromotionAction->setType(CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT);
        $catalogPromotionAction->setConfiguration(['amount' => $discount]);

        $catalogPromotion->addAction($catalogPromotionAction);

        $this->entityManager->flush();
    }

    /**
     * @Given /^there is a catalog promotion "([^"]*)" that reduces price by ("[^"]+") and applies on ("[^"]+" variant) and ("[^"]+" variant)$/
     * @Given /^there is a catalog promotion "([^"]*)" that reduces price by ("[^"]+") and applies on ("[^"]+" variant)$/
     */
    public function thereIsACatalogPromotionThatReducesPriceByAndAppliesOn(
        string $name,
        float $discount,
        ProductVariantInterface ...$variants
    ): void {
        $variantCodes = [];
        foreach ($variants as $variant) {
            $variantCodes[] = $variant->getCode();
        }

        $this->createCatalogPromotion(
            $name,
            null,
            [],
            [[
                'type' => CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS,
                'configuration' => ['variants' => $variantCodes],
            ]],
            [[
                'type' => CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT,
                'configuration' => ['amount' => $discount],
            ]]
        );

        $this->entityManager->flush();
    }

    /**
     * @Given /^there is a catalog promotion "([^"]*)" available in ("[^"]+" channel) that reduces price by ("[^"]+") and applies on ("[^"]+" variant)$/
     */
    public function thereIsACatalogPromotionAvailableInChannelThatReducesPriceByAndAppliesOnVariant(
        string $name,
        ChannelInterface $channel,
        float $discount,
        ProductVariantInterface $variant
    ): void {
        $this->createCatalogPromotion(
            $name,
            null,
            [$channel->getCode()],
            [[
                'type' => CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS,
                'configuration' => ['variants' => [$variant->getCode()]],
            ]],
            [[
                'type' => CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT,
                'configuration' => ['amount' => $discount],
            ]]
        );

        $this->entityManager->flush();
    }

    /**
     * @Given /^there is another catalog promotion "([^"]*)" available in ("[^"]+" channel) and ("[^"]+" channel) that reduces price by ("[^"]+") and applies on ("[^"]+" variant)$/
     */
    public function thereIsAnotherCatalogPromotionAvailableInChannelsThatReducesPriceByAndAppliesOnVariant(
        string $name,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
        float $discount,
        ProductVariantInterface $variant
    ): void {
        $this->createCatalogPromotion(
            $name,
            null,
            [
                $firstChannel->getCode(),
                $secondChannel->getCode()
            ],
            [[
                'type' => CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS,
                'configuration' => ['variants' => [$variant->getCode()]],
            ]],
            [[
                'type' => CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT,
                'configuration' => ['amount' => $discount],
            ]]
        );

        $this->entityManager->flush();
    }

    /**
     * @Given /^there is another catalog promotion "([^"]*)" available in ("[^"]+" channel) that reduces price by ("[^"]+") and applies on ("[^"]+" variant)$/
     */
    public function thereIsAnotherCatalogPromotionAvailableInChannelThatReducesPriceByAndAppliesOnVariant(
        string $name,
        ChannelInterface $channel,
        float $discount,
        ProductVariantInterface $variant
    ): void {
        $this->createCatalogPromotion(
            $name,
            null,
            [$channel->getCode()],
            [[
                'type' => CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS,
                'configuration' => ['variants' => [$variant->getCode()]],
            ]],
            [[
                'type' => CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT,
                'configuration' => ['amount' => $discount],
            ]]
        );

        $this->entityManager->flush();
    }

    /**
     * @When the :catalogPromotion catalog promotion is no longer available
     */
    public function theAdministratorMakesThisCatalogPromotionUnavailableInTheChannel(
        CatalogPromotionInterface $catalogPromotion
    ): void {
        foreach ($this->channelRepository->findAll() as $channel) {
            $catalogPromotion->removeChannel($channel);
        }
        $this->entityManager->persist($catalogPromotion);
        $this->entityManager->flush();

        $this->eventBus->dispatch(new CatalogPromotionUpdated($catalogPromotion->getCode()));
    }

    private function createCatalogPromotion(
        string $name,
        ?string $code = null,
        array $channels = [],
        array $rules = [],
        array $actions = []
    ): CatalogPromotionInterface {
        if (empty($channels) && $this->sharedStorage->has('channel')) {
            $channels = [$this->sharedStorage->get('channel')];
        }

        /** @var CatalogPromotionInterface $catalogPromotion */
        $catalogPromotion = $this->catalogPromotionExampleFactory->create([
            'name' => $name,
            'code' => $code,
            'channels' => $channels,
            'actions' => $actions,
            'rules' => $rules,
            'description' => $name . ' description'
        ]);

        $this->entityManager->persist($catalogPromotion);
        $this->sharedStorage->set('catalog_promotion', $catalogPromotion);

        return $catalogPromotion;
    }
}
