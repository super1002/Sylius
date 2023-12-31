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

namespace spec\Sylius\Bundle\MoneyBundle\Form\DataTransformer;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;

final class SyliusMoneyTransformerSpec extends ObjectBehavior
{
    function it_extends_money_to_localized_string_transformer_class(): void
    {
        $this->shouldHaveType(MoneyToLocalizedStringTransformer::class);
    }

    function it_returns_null_if_empty_string_given(): void
    {
        $this->reverseTransform('')->shouldReturn(null);
    }

    function it_converts_string_to_an_integer(): void
    {
        $this->beConstructedWith(null, null, null, 100);
        $this->reverseTransform('4.10')->shouldReturn(410);
    }
}
