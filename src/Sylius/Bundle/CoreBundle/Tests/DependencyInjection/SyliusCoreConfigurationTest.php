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

namespace Sylius\Bundle\CoreBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\Partial\PartialProcessor;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

final class SyliusCoreConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /** @test */
    public function it_does_not_define_that_previous_priorities_should_be_brought_back_for_order_processing(): void
    {
        $this->assertProcessedConfigurationEquals(
            [[]],
            ['processing_shipments_before_prices' => false],
            'processing_shipments_before_prices',
        );
    }

    /** @test */
    public function it_allows_to_define_that_previous_priorities_should_be_brought_back_for_order_processing(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['processing_shipments_before_prices' => true]],
            ['processing_shipments_before_prices' => true],
            'processing_shipments_before_prices',
        );
    }

    /** @test */
    public function it_does_not_allow_to_define_previous_priorities_with_values_other_then_bool(): void
    {
        $this->expectException(InvalidTypeException::class);

        (new PartialProcessor())->processConfiguration(
            $this->getConfiguration(),
            'processing_shipments_before_prices',
            [['processing_shipments_before_prices' => 'yolo']]
        );
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }
}
