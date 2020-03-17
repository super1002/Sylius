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

namespace Sylius\Bundle\ApiBundle\Applicator;

use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Core\ProductReviewTransitions;
use Sylius\Component\Review\Model\ReviewInterface;

final class ProductReviewStateMachineTransitionApplicator
{
    /** @var StateMachineFactoryInterface $stateMachineFactory */
    private $stateMachineFactory;

    public function __construct(StateMachineFactoryInterface $stateMachineFactory)
    {
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function accept(ReviewInterface $data): ReviewInterface
    {
        $stateMachine = $this->stateMachineFactory->get($data, ProductReviewTransitions::GRAPH);

        $stateMachine->apply(ProductReviewTransitions::TRANSITION_ACCEPT);

        return $data;
    }

    public function reject(ReviewInterface $data): ReviewInterface
    {
        $stateMachine = $this->stateMachineFactory->get($data, ProductReviewTransitions::GRAPH);

        $stateMachine->apply(ProductReviewTransitions::TRANSITION_REJECT);

        return $data;
    }
}
