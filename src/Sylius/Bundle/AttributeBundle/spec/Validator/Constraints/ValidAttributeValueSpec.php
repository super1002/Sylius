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

namespace spec\Sylius\Bundle\AttributeBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidAttributeValue;
use Symfony\Component\Validator\Constraint;

final class ValidAttributeValueSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(ValidAttributeValue::class);
    }

    function it_is_constraint(): void
    {
        $this->shouldHaveType(Constraint::class);
    }

    function it_has_targets(): void
    {
        $this->getTargets()->shouldReturn(Constraint::CLASS_CONSTRAINT);
    }

    function it_is_validated_by_specific_validator(): void
    {
        $this->validatedBy()->shouldReturn('sylius_valid_attribute_value_validator');
    }
}
