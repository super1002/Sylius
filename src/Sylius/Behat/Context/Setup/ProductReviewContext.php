<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
final class ProductReviewContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $productReviewFactory;

    /**
     * @var RepositoryInterface
     */
    private $productReviewRepository;

    /**
     * @var StateMachineFactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $productReviewFactory
     * @param RepositoryInterface $productReviewRepository
     * @param StateMachineFactoryInterface $stateMachineFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $productReviewFactory,
        RepositoryInterface $productReviewRepository,
        StateMachineFactoryInterface $stateMachineFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productReviewFactory = $productReviewFactory;
        $this->productReviewRepository = $productReviewRepository;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    /**
     * @Given /^(this product) has one review$/
     */
    public function productHasAReview(ProductInterface $product)
    {
        $review = $this->createProductReview($product, 'Title', 5, 'Comment');

        $this->productReviewRepository->add($review);
    }

    /**
     * @Given /^(this product) has(?:| also) a review titled "([^"]+)" and rated (\d+) added by (customer "[^"]+")(?:|, created (\d+) days ago)$/
     */
    public function thisProductHasAReviewTitledAndRatedAddedByCustomer(
        ProductInterface $product,
        $title,
        $rating,
        CustomerInterface $customer,
        $daysSinceCreation = null
    ) {
        $review = $this->createProductReview($product, $title, $rating, $title, $customer);
        if (null !== $daysSinceCreation) {
            $review->setCreatedAt(new \DateTime('-'.$daysSinceCreation.' days'));
        }

        $this->productReviewRepository->add($review);
    }

    /**
     * @Given /^(this product) has(?:| also) a review titled "([^"]+)" and rated (\d+) added by (customer "[^"]+") which is not accepted yet$/
     */
    public function thisProductHasAReviewTitledAndRatedAddedByCustomerWhichIsNotAcceptedYet(
        ProductInterface $product,
        $title,
        $rating,
        CustomerInterface $customer
    ) {
        $review = $this->createProductReview($product, $title, $rating, $title, $customer, null);

        $this->productReviewRepository->add($review);
    }

    /**
     * @Given /^(this product) has accepted reviews rated (\d+), (\d+), (\d+), (\d+) and (\d+)$/
     */
    public function thisProductHasAcceptedReviewsRated(ProductInterface $product, ...$rates)
    {
        $customer = $this->sharedStorage->get('customer');
        foreach ($rates as $key => $rate) {
            $review = $this->createProductReview($product, 'Title '.$key, $rate, 'Comment '.$key, $customer);
            $this->productReviewRepository->add($review);
        }
    }

    /**
     * @param ProductInterface $product
     * @param string $title
     * @param int $rating
     * @param string $comment
     * @param CustomerInterface|null $customer
     * @param string $transition
     *
     * @return ReviewInterface
     */
    private function createProductReview(
        ProductInterface $product,
        $title,
        $rating,
        $comment,
        CustomerInterface $customer = null,
        $transition = 'accept'
    ) {
        /** @var ReviewInterface $review */
        $review = $this->productReviewFactory->createNew();
        $review->setTitle($title);
        $review->setRating($rating);
        $review->setComment($comment);
        $review->setReviewSubject($product);
        $review->setAuthor($customer);

        $product->addReview($review);

        if (null !== $transition) {
            $stateMachine = $this->stateMachineFactory->get($review, 'sylius_product_review');
            $stateMachine->apply($transition);
        }

        return $review;
    }
}
