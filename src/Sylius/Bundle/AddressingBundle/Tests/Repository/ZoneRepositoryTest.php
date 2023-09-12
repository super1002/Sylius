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

namespace Sylius\Bundle\AddressingBundle\Tests\Repository;

use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Bundle\AddressingBundle\Repository\ZoneRepositoryInterface;
use Sylius\Component\Addressing\Model\Address;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ZoneRepositoryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures();
    }

    /** @test */
    public function it_finds_a_single_zone_by_address_prioritising_province_type(): void
    {
        $address = new Address();
        $address->setCountryCode('PL');
        $address->setProvinceCode('MA');

        $repository = $this->getRepository();
        $zone = $repository->findOneByAddress($address);

        $this->assertNotNull($zone);
        $this->assertSame('POLISH_PROVINCES', $zone->getCode());
    }

    /** @test */
    public function it_finds_a_single_zone_by_address_prioritising_country_type_when_no_province_is_found(): void
    {
        $address = new Address();
        $address->setCountryCode('PL');
        $address->setProvinceCode('ZZ');

        $repository = $this->getRepository();
        $zone = $repository->findOneByAddress($address);

        $this->assertNotNull($zone);
        $this->assertSame('EU', $zone->getCode());
    }

    /** @test */
    public function it_finds_all_zones_for_a_given_address_with_both_province_and_country(): void
    {
        $address = new Address();
        $address->setCountryCode('PL');
        $address->setProvinceCode('MA');

        $repository = $this->getRepository();
        $zones = [];

        foreach ($repository->findAllByAddress($address) as $zone) {
            $zones[$zone->getCode()] = $zone;
        }

        $this->assertCount(3, $zones);
        $this->assertArrayHasKey('EU', $zones);
        $this->assertArrayHasKey('VISEGRAD_GROUP', $zones);
        $this->assertArrayHasKey('POLISH_PROVINCES', $zones);
    }

    /** @test */
    public function it_finds_all_zones_for_a_given_address_with_only_country(): void
    {
        $address = new Address();
        $address->setCountryCode('PL');

        $repository = $this->getRepository();
        $zones = [];

        foreach ($repository->findAllByAddress($address) as $zone) {
            $zones[$zone->getCode()] = $zone;
        }

        $this->assertCount(2, $zones);
        $this->assertArrayHasKey('EU', $zones);
        $this->assertArrayHasKey('VISEGRAD_GROUP', $zones);
    }

    /** @test */
    public function it_finds_all_zones_for_a_given_address_with_restricting_by_scope_if_provided(): void
    {
        $address = new Address();
        $address->setCountryCode('PL');
        $address->setProvinceCode('MA');

        $repository = $this->getRepository();
        $zones = [];

        foreach ($repository->findAllByAddress($address, 'only_eu') as $zone) {
            $zones[$zone->getCode()] = $zone;
        }

        $this->assertCount(1, $zones);
        $this->assertArrayHasKey('EU', $zones);
        $this->assertArrayNotHasKey('VISEGRAD_GROUP', $zones);
        $this->assertArrayNotHasKey('POLISH_PROVINCES', $zones);
    }

    /** @test */
    public function it_finds_all_zones_by_passing_a_child_zone(): void
    {
        $repository = $this->getRepository();
        $zones = [];

        $visegradGroupZone = $repository->findOneBy(['code' => 'VISEGRAD_GROUP']);
        foreach ($repository->findAllByZones([$visegradGroupZone]) as $zone) {
            $zones[$zone->getCode()] = $zone;
        }

        $this->assertCount(2, $zones);
        $this->assertArrayHasKey('EU_MIDDLE', $zones);
        $this->assertArrayHasKey('NATO', $zones);
    }

    /** @test */
    public function it_finds_all_zones_by_passing_a_child_zone_with_reistricting_by_scope_if_provided(): void
    {
        $repository = $this->getRepository();
        $zones = [];

        /** @var ZoneInterface $visegradGroupZone */
        $visegradGroupZone = $repository->findOneBy(['code' => 'VISEGRAD_GROUP']);

        foreach ($repository->findAllByZones([$visegradGroupZone], 'only_eu') as $zone) {
            $zones[$zone->getCode()] = $zone;
        }

        $this->assertCount(1, $zones);
        $this->assertArrayHasKey('EU_MIDDLE', $zones);
        $this->assertArrayNotHasKey('NATO', $zones);
    }

    private function getRepository(): ZoneRepositoryInterface
    {
        /** @var ZoneRepositoryInterface $repository */
        $repository = self::getContainer()->get('sylius.repository.zone');

        return $repository;
    }

    private function loadFixtures(): void
    {
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        $fixtureLoader->load([
            __DIR__ . '/ZoneRepositoryTest/fixtures.yaml',
        ], [], [], PurgeMode::createDeleteMode());
    }
}
