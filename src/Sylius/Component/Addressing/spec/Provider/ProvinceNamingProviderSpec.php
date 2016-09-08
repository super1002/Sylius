<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Addressing\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Component\Addressing\Provider\ProvinceNamingProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ProvinceNamingProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $provinceRepository)
    {
        $this->beConstructedWith($provinceRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Addressing\Provider\ProvinceNamingProvider');
    }

    function it_implements_province_name_provider_interface()
    {
        $this->shouldHaveType(ProvinceNamingProviderInterface::class);
    }

    function it_throws_invalid_argument_exception_when_province_with_given_code_is_not_found(
        RepositoryInterface $provinceRepository,
        AddressInterface $address
    ) {
        $address->getProvinceCode()->willReturn('ZZ-TOP');
        $provinceRepository->findOneBy(['code' => 'ZZ-TOP'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('getName', [$address]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('getAbbreviation', [$address]);
    }

    function it_gets_province_name_if_province_with_given_code_exist_in_database(
        RepositoryInterface $provinceRepository,
        ProvinceInterface $province,
        AddressInterface $address
    ) {
        $address->getProvinceCode()->willReturn('IE-UL');
        $provinceRepository->findOneBy(['code' => 'IE-UL'])->willReturn($province);
        $province->getName()->willReturn('Ulster');

        $this->getName($address)->shouldReturn('Ulster');
    }

    function it_gets_province_name_form_address_if_address_do_not_has_province_code(AddressInterface $address)
    {
        $address->getProvinceCode()->willReturn(null);
        $address->getProvinceName()->willReturn('Ulster');

        $this->getName($address)->shouldReturn('Ulster');
    }

    function it_returns_nothing_if_province_name_and_code_are_not_given_in_an_address(AddressInterface $address)
    {
        $address->getProvinceCode()->willReturn(null);
        $address->getProvinceName()->willReturn(null);

        $this->getName($address)->shouldReturn(null);
        $this->getAbbreviation($address)->shouldReturn(null);

    }

    function it_gets_province_abbreviation_by_its_code_if_province_exists_in_database(
        RepositoryInterface $provinceRepository,
        ProvinceInterface $province,
        AddressInterface $address
    ) {
        $address->getProvinceCode()->willReturn('IE-UL');
        $provinceRepository->findOneBy(['code' => 'IE-UL'])->willReturn($province);
        $province->getAbbreviation()->willReturn('ULS');

        $this->getAbbreviation($address)->shouldReturn('ULS');
    }

    function it_gets_province_name_if_its_abbreviation_is_not_set_but_province_exists_in_database(
        RepositoryInterface $provinceRepository,
        ProvinceInterface $province,
        AddressInterface $address
    ) {
        $address->getProvinceCode()->willReturn('IE-UL');
        $provinceRepository->findOneBy(['code' => 'IE-UL'])->willReturn($province);
        $province->getAbbreviation()->willReturn(null);
        $province->getName()->willReturn('Ulster');

        $this->getAbbreviation($address)->shouldReturn('Ulster');
    }

    function it_gets_province_name_form_address_if_its_abbreviation_is_not_set_and_province_not_exists_in_database(AddressInterface $address)
    {
        $address->getProvinceCode()->willReturn(null);
        $address->getProvinceName()->willReturn('Ulster');

        $this->getAbbreviation($address)->shouldReturn('Ulster');
    }
}
