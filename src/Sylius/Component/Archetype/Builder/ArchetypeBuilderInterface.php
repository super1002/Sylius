<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Archetype\Builder;

use Sylius\Component\Archetype\Model\ArchetypeSubjectInterface;
use Sylius\Component\Archetype\Model\ArchetypeInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
interface ArchetypeBuilderInterface
{
    /**
     * Build the archetype of product.
     *
     * @param ArchetypeInterface        $archetype
     * @param ArchetypeSubjectInterface $subject
     */
    public function build(ArchetypeInterface $archetype, ArchetypeSubjectInterface $subject);
}
