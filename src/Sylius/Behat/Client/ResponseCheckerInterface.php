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

namespace Sylius\Behat\Client;

use Symfony\Component\HttpFoundation\Response;

interface ResponseCheckerInterface
{
    public function countCollectionItems(Response $response): int;

    public function getCollection(Response $response): array;

    public function getCollectionItemsWithValue(Response $response, string $key, string $value): array;

    public function getValue(Response $response, string $key);

    public function getError(Response $response): string;

    public function isCreationSuccessful(Response $response): bool;

    public function isUpdateSuccessful(Response $response): bool;

    public function isDeletionSuccessful(Response $response): bool;

    /** @param string|int $value */
    public function hasValue(Response $response, string $key, $value): bool;

    public function hasItemWithValue(Response $response, string $key, $value): bool;

    public function hasItemOnPositionWithValue(Response $response, int $position, string $key, string $value): bool;

    public function hasItemWithTranslation(Response $response, string $locale, string $key, string $translation): bool;
}
