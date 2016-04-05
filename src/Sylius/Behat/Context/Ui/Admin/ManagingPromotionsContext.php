<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Promotion\CreatePageInterface;
use Sylius\Behat\Page\Admin\Promotion\IndexPageInterface;
use Sylius\Behat\Page\Admin\Promotion\UpdatePageInterface;
use Sylius\Behat\Service\CurrentPageResolverInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ManagingPromotionsContext implements Context
{
    const RESOURCE_NAME = 'promotion';

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given I want to create a new promotion
     */
    public function iWantToCreateANewPromotion()
    {
        $this->createPage->open();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null)
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I name it :name
     * @When I do not name it
     */
    public function iNameIt($name = null)
    {
        $this->createPage->nameIt($name);
    }

    /**
     * @Then the promotion :promotionName should appear in the registry
     */
    public function thePromotionShouldAppearInTheRegistry($promotionName)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isResourceOnPage(['name' => $promotionName]),
            sprintf('Promotion with name %s has not been found.', $promotionName)
        );
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedItHasBeenSuccessfulCreation()
    {
        $this->notificationChecker->checkCreationNotification(self::RESOURCE_NAME);
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter promotion %s.', $element));
    }

    /**
     * @Then I should be notified that promotion with this code already exists
     */
    public function iShouldBeNotifiedThatPromotionWithThisCodeAlreadyExists()
    {
        Assert::true(
            $this->createPage->checkValidationMessageFor('code', 'The promotion with given code already exists.'),
            'Unique code violation message should appear on page, but it does not.'
        );
    }

    /**
     * @Then promotion with :element :name should not be added
     */
    public function promotionWithElementValueShouldNotBeAdded($element, $name)
    {
        $this->indexPage->open();

        Assert::false(
            $this->indexPage->isResourceOnPage([$element => $name]),
            sprintf('Promotion with %s %s has been created, but it should not.', $element, $name)
        );
    }

    /**
     * @Then there should still be only one promotion with :element :code
     */
    public function thereShouldStillBeOnlyOnePromotionWith($element, $code)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isResourceOnPage([$element => $code]),
            sprintf('Promotion with %s %s cannot be founded.', $element, $code)
        );
    }

    /**
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage($element, $expectedMessage)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm($this->createPage, $this->updatePage);

        Assert::true(
            $currentPage->checkValidationMessageFor($element, $expectedMessage),
            sprintf('Promotion %s should be required.', $element)
        );
    }
}
