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
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;

interface CatalogPromotionInterface extends ResourceInterface, CodeAwareInterface, TranslatableInterface
{
    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getLabel(): ?string;

    public function setLabel(?string $label): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;

    /** @return CatalogPromotionTranslationInterface */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
