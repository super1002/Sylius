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

namespace spec\Sylius\Component\Core\Dashboard;

use PhpSpec\ObjectBehavior;

final class SalesSummarySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(
            new \DateTime('01-02-2010'),
            new \DateTime('31-01-2011'),
            'month',
            [9 => 1200, 10 => 400, 11 => 500],
            'm'
        );
    }

    function it_has_intervals_list(): void
    {
        $this->getIntervals()->shouldReturn(
            [1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ,11, 12]
        );
    }

    function it_has_sales_list(): void
    {
        $this->getSales()->shouldReturn(
            ['0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '12.00', '4.00', '5.00', '0.00']
        );
    }
}
