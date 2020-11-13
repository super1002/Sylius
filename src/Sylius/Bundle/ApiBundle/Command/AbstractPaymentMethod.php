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

namespace Sylius\Bundle\ApiBundle\Command;

/** @experimental */
abstract class AbstractPaymentMethod implements OrderTokenValueAwareInterface, SubresourceIdAwareInterface
{
    /** @var string|null */
    public $orderTokenValue;

    /**
     * @psalm-immutable
     *
     * @var string
     */
    public $paymentId;

    /**
     * @psalm-immutable
     *
     * @var string
     */
    public $paymentMethodCode;

    public function __construct(string $paymentMethodCode)
    {
        $this->paymentMethodCode = $paymentMethodCode;
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }

    public function setOrderTokenValue(?string $orderTokenValue): void
    {
        $this->orderTokenValue = $orderTokenValue;
    }

    public function getSubresourceId(): ?string
    {
        return $this->paymentId;
    }

    public function setSubresourceId(?string $subresourceId): void
    {
        $this->paymentId = $subresourceId;
    }

    public function getSubresourceIdAttributeKey(): string
    {
        return 'paymentId';
    }
}
