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

namespace Sylius\Bundle\CoreBundle\Form\Type\Taxon;

use Sylius\Bundle\CoreBundle\Form\Type\ImageType;

final class TaxonImageType extends ImageType
{
    public function getBlockPrefix(): string
    {
        return 'sylius_taxon_image';
    }
}
