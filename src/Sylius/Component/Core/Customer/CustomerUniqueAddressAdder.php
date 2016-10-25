<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Customer;

use Sylius\Component\Addressing\Comparator\AddressComparatorInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class CustomerUniqueAddressAdder implements AddressAdderInterface
{
    /**
     * @var AddressComparatorInterface
     */
    private $addressComparator;

    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @param AddressComparatorInterface $addressComparator
     * @param CustomerContextInterface $customerContext
     */
    public function __construct(
        AddressComparatorInterface $addressComparator,
        CustomerContextInterface $customerContext
    ) {
        $this->addressComparator = $addressComparator;
        $this->customerContext = $customerContext;
    }

    /**
     * {@inheritdoc}
     */
    public function add(AddressInterface $address)
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerContext->getCustomer();
        if (null === $customer) {
            return;
        }

        foreach ($customer->getAddresses() as $customerAddress) {
            if ($this->addressComparator->same($customerAddress, $address)) {
                return;
            }
        }

        $customer->addAddress($address);
    }
}
