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

namespace Sylius\Component\Promotion\Model;

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface CatalogPromotionInterface extends ResourceInterface, CodeAwareInterface, TranslatableInterface
{
    public const TYPE_PERCENTAGE_DISCOUNT = 'percentage_product_discount';

    public const TYPE_CONTAINS_VARIANTS = 'contains_variants';

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getLabel(): ?string;

    public function setLabel(?string $label): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;

    /** @return CatalogPromotionTranslationInterface */
    public function getTranslation(?string $locale = null): TranslationInterface;

    /**
     * @return Collection|PromotionRuleInterface[]
     *
     * @psalm-return Collection<array-key, CatalogPromotionRuleInterface>
     */
    public function getRules(): Collection;

    public function addRule(CatalogPromotionRuleInterface $rule): void;

    public function removeRule(CatalogPromotionRuleInterface $rule): void;
}
