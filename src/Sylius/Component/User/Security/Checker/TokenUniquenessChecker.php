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

namespace Sylius\Component\User\Security\Checker;

use Sylius\Component\Resource\Repository\RepositoryInterface;

final class TokenUniquenessChecker implements UniquenessCheckerInterface
{
    public function __construct(private RepositoryInterface $repository, private string $tokenFieldName)
    {
    }

    public function isUnique(string $token): bool
    {
        return null === $this->repository->findOneBy([$this->tokenFieldName => $token]);
    }
}
