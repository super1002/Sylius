<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Payment\Repository\PaymentRepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class PaymentRepository extends EntityRepository implements PaymentRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByOrderAndId($orderId, $id)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder
            ->where('o.order = :orderId')
            ->andWhere('o.id = :id')
            ->setParameter('orderId', $orderId)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
