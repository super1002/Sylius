<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ArrayToStringTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    private $delimiter;

    /**
     * @param string $delimiter
     */
    public function __construct($delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        Assert::isArray($value);
        if (empty($value)) {
            return '';
        }

        return implode($this->delimiter, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        Assert::string($value);
        if ('' === $value) {
            return [];
        }

        return explode($this->delimiter, $value);
    }
}
