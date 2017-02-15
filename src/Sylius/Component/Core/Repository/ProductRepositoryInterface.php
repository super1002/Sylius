<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ProductRepositoryInterface extends BaseProductRepositoryInterface
{
    /**
     * @param string $locale
     * @param mixed|null $taxonId
     *
     * @return QueryBuilder
     */
    public function createListQueryBuilder($locale, $taxonId = null);

    /**
     * @param ChannelInterface $channel
     * @param TaxonInterface $taxon
     * @param string $locale
     * @param array $sorting
     *
     * @return QueryBuilder
     */
    public function createShopListQueryBuilder(ChannelInterface $channel, TaxonInterface $taxon, $locale, array $sorting);

    /**
     * @param ChannelInterface $channel
     * @param string $locale
     * @param int $count
     *
     * @return ProductInterface[]
     */
    public function findLatestByChannel(ChannelInterface $channel, $locale, $count);

    /**
     * @param string $slug
     * @param ChannelInterface $channel
     *
     * @return ProductInterface|null
     */
    public function findOneBySlugAndChannel($slug, ChannelInterface $channel);
    /**
     * @param string $slug
     *
     * @return ProductInterface|null
     */
    public function findOneBySlug($slug);
}
