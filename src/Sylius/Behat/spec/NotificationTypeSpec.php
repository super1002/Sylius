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

namespace spec\Sylius\Behat;

use PhpSpec\ObjectBehavior;

final class NotificationTypeSpec extends ObjectBehavior
{
    function it_initialize_with_success_value()
    {
        $this->beConstructedThrough('success');
        $this->__toString()->shouldReturn('success');
    }

    function it_initialize_with_failure_value()
    {
        $this->beConstructedThrough('failure');
        $this->__toString()->shouldReturn('failure');
    }
}
