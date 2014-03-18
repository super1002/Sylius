<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Stock extends Constraint
{
    public $message = '%product% does not have sufficient stock.';

    public function validatedBy()
    {
        return 'sylius_stock';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
