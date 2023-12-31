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

namespace spec\Sylius\Component\Product\Generator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class CartesianSetBuilderSpec extends ObjectBehavior
{
    function it_requires_an_array_of_set_tuples_to_build_from(): void
    {
        $tupleSetNotInArray = ['a', 'b', 'c'];

        $this->shouldThrow(\InvalidArgumentException::class)->duringBuild($tupleSetNotInArray, Argument::any());
    }

    function it_requires_at_least_one_set_tuple(): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->duringBuild([], Argument::any());
    }

    function it_returns_the_same_set_as_the_cartesian_product_when_only_one_was_given(): void
    {
        $set = ['a', 'b', 'c'];

        $this->build([$set], false)->shouldReturn($set);
    }

    function it_builds_the_cartesian_product_set_from_two_sets(): void
    {
        $setA = ['a', 'b', 'c'];
        $setB = ['1', '2', '3'];

        $this->build([$setA, $setB], false)->shouldReturn([
            ['a', '1'],
            ['a', '2'],
            ['a', '3'],

            ['b', '1'],
            ['b', '2'],
            ['b', '3'],

            ['c', '1'],
            ['c', '2'],
            ['c', '3'],
        ]);
    }

    function it_builds_the_cartesian_product_set_from_more_than_two_sets(): void
    {
        $setA = ['a', 'b', 'c'];
        $setB = ['1', '2', '3'];
        $setC = ['!', '@', '$'];

        $this->build([$setA, $setB, $setC], false)->shouldReturn([
            ['a', '1', '!'], ['a', '1', '@'], ['a', '1', '$'],
            ['a', '2', '!'], ['a', '2', '@'], ['a', '2', '$'],
            ['a', '3', '!'], ['a', '3', '@'], ['a', '3', '$'],

            ['b', '1', '!'], ['b', '1', '@'], ['b', '1', '$'],
            ['b', '2', '!'], ['b', '2', '@'], ['b', '2', '$'],
            ['b', '3', '!'], ['b', '3', '@'], ['b', '3', '$'],

            ['c', '1', '!'], ['c', '1', '@'], ['c', '1', '$'],
            ['c', '2', '!'], ['c', '2', '@'], ['c', '2', '$'],
            ['c', '3', '!'], ['c', '3', '@'], ['c', '3', '$'],
        ]);
    }
}
