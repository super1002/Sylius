<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Converter;

use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use InvalidArgumentException;

class CurrencyConverter implements CurrencyConverterInterface
{
    protected $exchangeRateRepository;

    public function __construct(RepositoryInterface $exchangeRateRepository)
    {
        $this->exchangeRateRepository = $exchangeRateRepository;
    }

    public function convert($value, $currency)
    {
        $exchangeRate = $this->exchangeRateRepository->findOneByCurrency($currency);
        if (null === $exchangeRate) {
            throw new InvalidArgumentException(sprintf('Exchange rate for "%s" not found.', $currency));
        }

        return $value / $exchangeRate->getRate();
    }
}
