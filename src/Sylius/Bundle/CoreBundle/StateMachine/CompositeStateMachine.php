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

namespace Sylius\Bundle\CoreBundle\StateMachine;

use Exception;
use Traversable;
use Webmozart\Assert\Assert;

class CompositeStateMachine implements StateMachineInterface
{
    /** @var array<StateMachineInterface> */
    private array $stateMachineAdapters;

    public function __construct(iterable $stateMachineAdapters)
    {
        Assert::notEmpty($stateMachineAdapters, 'At least one state machine adapter should be provided.');
        Assert::allIsInstanceOf(
            $stateMachineAdapters,
            StateMachineInterface::class,
            sprintf('All state machine adapters should implement the "%s" interface.', StateMachineInterface::class),
        );
        $this->stateMachineAdapters = $stateMachineAdapters instanceof Traversable ? iterator_to_array($stateMachineAdapters) : $stateMachineAdapters;
    }

    /**
     * @throws Exception
     */
    public function can(object $subject, string $graphName, string $transition): bool
    {
        $lastException = null;

        foreach ($this->stateMachineAdapters as $stateMachineAdapter) {
            try {
                return $stateMachineAdapter->can($subject, $graphName, $transition);
            } catch (Exception $exception) {
                $lastException = $exception;
            }
        }

        throw $lastException;
    }

    /**
     * @throws Exception
     */
    public function apply(object $subject, string $graphName, string $transition, array $context = []): void
    {
        $lastException = null;

        foreach ($this->stateMachineAdapters as $stateMachineAdapter) {
            try {
                $stateMachineAdapter->apply($subject, $graphName, $transition, $context);

                return;
            } catch (Exception $exception) {
                $lastException = $exception;
            }
        }

        throw $lastException;
    }

    /**
     * @throws Exception
     */
    public function getEnabledTransition(object $subject, string $graphName): array
    {
        $lastException = null;

        foreach ($this->stateMachineAdapters as $stateMachineAdapter) {
            try {
                return $stateMachineAdapter->getEnabledTransition($subject, $graphName);
            } catch (Exception $exception) {
                $lastException = $exception;
            }
        }

        throw $lastException;
    }
}
