<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Grid\DataExtractor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Grid\DataExtractor\DataExtractorInterface;
use Sylius\Component\Grid\DataExtractor\PropertyAccessDataExtractor;
use Sylius\Component\Grid\Definition\Field;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @mixin PropertyAccessDataExtractor
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PropertyAccessDataExtractorSpec extends ObjectBehavior
{
    function let(PropertyAccessorInterface $propertyAccessor)
    {
        $this->beConstructedWith($propertyAccessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Grid\DataExtractor\PropertyAccessDataExtractor');
    }

    function it_is_a_data_extractor()
    {
        $this->shouldImplement(DataExtractorInterface::class);
    }
    
    function it_uses_property_accessor_to_extract_the_data(PropertyAccessorInterface $propertyAccessor, Field $field)
    {
        $field->getPath()->willReturn('foo');
        $propertyAccessor->getValue(['foo' => 'bar'], 'foo')->willReturn('Value');
        
        $this->get($field, ['foo' => 'bar'])->shouldReturn('Value');
    }
}
