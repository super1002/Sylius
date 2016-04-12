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
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Zone\UpdatePageInterface;
use Sylius\Behat\Page\Admin\Zone\CreatePageInterface;
use Sylius\Behat\Service\CurrentPageResolverInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\NotificationType;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ManagingZoneContext implements Context
{
    const RESOURCE_NAME = 'zone';

    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

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
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given I want to create a new zone with :memberType members
     */
    public function iWantToCreateANewZoneWithMembers($memberType)
    {
        $this->createPage->open(['type' => $memberType]);
    }

    /**
     * @Given I want to see all zones in store
     */
    public function iWantToSeeAllZonesInStore()
    {
        $this->indexPage->open();
    }

    /**
     * @Given /^I want to modify the (zone named "[^"]+")$/
     */
    public function iWantToModifyAZoneNamed(ZoneInterface $zone)
    {
        $this->updatePage->open(['id' => $zone->getId()]);
    }

    /**
     * @When /^I delete (zone named "([^"]*)")$/
     */
    public function iDeleteZoneNamed(ZoneInterface $zone)
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['name' => $zone->getName(), 'code' => $zone->getCode()]);
    }

    /**
     * @When /^I remove (the "([^"]*)" province member)$/
     * @When /^I remove (the "([^"]*)" country member)$/
     * @When /^I remove (the "([^"]*)" zone member)$/
     */
    public function iRemoveTheMember(ZoneMemberInterface $zoneMember)
    {
        $this->updatePage->removeMember($zoneMember);
    }

    /**
     * @When I rename it to :name
     */
    public function iRenameItTo($name)
    {
        $this->updatePage->nameIt($name);
    }

    /**
     * @When I name it :name
     */
    public function iNameIt($name)
    {
        $this->createPage->nameIt($name);
    }

    /**
     * @When I specify its code as :code
     */
    public function iSpecifyItsCodeAs($code)
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I do not specify its code
     */
    public function iDoNotSpecifyItsCode()
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When I do not specify its name
     */
    public function iDoNotSpecifyItsName()
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When I do not add a country member
     */
    public function iDoNotAddACountryMember()
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When I add a country :name
     * @When I add a province :name
     * @When I add a zone :name
     */
    public function iAddACountry($name)
    {
        $this->createPage->addMember();
        $this->createPage->chooseMember($name);
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
     * @When I save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then I should be notified about successful creation
     */
    public function iShouldBeNotifiedAboutSuccessfulCreation()
    {
        $this->notificationChecker->checkCreationNotification(self::RESOURCE_NAME);
    }

    /**
     * @Then I should be notified about successful edition
     */
    public function iShouldBeNotifiedAboutSuccessfulEdition()
    {
        $this->notificationChecker->checkEditionNotification(self::RESOURCE_NAME);
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted()
    {
        $this->notificationChecker->checkDeletionNotification(self::RESOURCE_NAME);
    }

    /**
     * @Then /^the (zone named "[^"]+") with (the "[^"]+" country member) should appear in the registry$/
     * @Then /^the (zone named "[^"]+") with (the "[^"]+" province member) should appear in the registry$/
     * @Then /^the (zone named "[^"]+") with (the "[^"]+" zone member) should appear in the registry$/
     */
    public function theZoneWithTheCountryShouldAppearInTheRegistry(ZoneInterface $zone, ZoneMemberInterface $zoneMember)
    {
        $this->assertZoneAndItsMember($zone, $zoneMember);
    }

    /**
     * @Then /^(this zone) should have only (the "([^"]*)" country member)$/
     * @Then /^(this zone) should have only (the "([^"]*)" province member)$/
     * @Then /^(this zone) should have only (the "([^"]*)" zone member)$/
     */
    public function thisZoneShouldHaveOnlyTheProvinceMember(ZoneInterface $zone, ZoneMemberInterface $zoneMember)
    {
        $this->assertZoneAndItsMember($zone, $zoneMember);

        Assert::eq(
            1,
            $this->updatePage->countMembers(),
            sprintf('Zone %s should have only %s zone member',$zone->getName(), $zoneMember->getCode())
        );
    }

    /**
     * @Then /^(this zone) name should be "([^"]*)"/
     */
    public function thisZoneNameShouldBe(ZoneInterface $zone, $name)
    {
        Assert::true(
            $this->updatePage->hasResourceValues(['code' => $zone->getCode(), 'name' => $name]),
            'Zone name should be modified but it is not'
        );
    }

    /**
     * @Then /^the code field should be disabled$/
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true(
            $this->updatePage->isCodeDisabled(),
            'Code field should be disabled but it is not'
        );
    }

    /**
     * @Then /^I should be notified that zone with this code already exists$/
     */
    public function iShouldBeNotifiedThatZoneWithThisCodeAlreadyExists()
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm($this->createPage, $this->updatePage);

        Assert::true(
            $currentPage->checkValidationMessageFor('code', 'Zone code must be unique.'),
            'Unique code violation message should appear on page, but it does not.'
        );
    }

    /**
     * @Then /^there should still be only one zone with code "([^"]*)"$/
     */
    public function thereShouldStillBeOnlyOneZoneWithCode($code)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isResourceOnPage(['code' => $code]),
            sprintf('Zone with code %s cannot be founded.', $code)
        );
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm($this->createPage, $this->updatePage);

        Assert::true(
            $currentPage->checkValidationMessageFor($element, sprintf('Please enter zone %s.', $element)),
            sprintf('I should be notified that zone %s should be required.', $element)
        );
    }

    /**
     * @Then zone with :element :value should not be added
     */
    public function zoneWithNameShouldNotBeAdded($element, $value)
    {
        $this->indexPage->open();

        Assert::false(
            $this->indexPage->isResourceOnPage([$element => $value]),
            sprintf('Zone with %s %s was created, but it should not.', $element, $value)
        );
    }

    /**
     * @Then /^I should be notified that at least one zone member is required$/
     */
    public function iShouldBeNotifiedThatAtLeastOneZoneMemberIsRequired()
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm($this->createPage, $this->updatePage);

        Assert::true(
            $currentPage->checkValidationMessageFor('member', 'Please add at least 1 zone member.'),
            sprintf('I should be notified that zone needs at least one zone member.')
        );
    }

    /**
     * @Then the type field should be disabled
     */
    public function theTypeFieldShouldBeDisabled()
    {
        Assert::true(
            $this->createPage->isTypeFieldDisabled(),
            'Type field should be disabled but it is not'
        );
    }

    /**
     * @Then it should be :type type
     */
    public function itShouldBeType($type)
    {
        Assert::true(
            $this->createPage->hasType($type),
            sprintf('Zone should be %s type', $type)
        );
    }

    /**
     * @Then the zone named :zoneName should no longer exist in the registry
     */
    public function thisZoneShouldNoLongerExistInTheRegistry($zoneName)
    {
        Assert::false(
            $this->indexPage->isResourceOnPage(['name' => $zoneName]),
            sprintf('Zone named %s should no longer exist', $zoneName)
        );
    }

    /**
     * @Then /^I should see (\d+) zones in the list$/
     */
    public function iShouldSeeZonesInTheList($number)
    {
        $resourcesOnPage = $this->indexPage->countAllResourcesOnPage();

        Assert::eq(
            $number,
            $resourcesOnPage,
            sprintf('On list should be %d zones but get %d', $number, $resourcesOnPage)
        );
    }

    /**
     * @Then /^I should see the (zone named "([^"]*)") in the list$/
     * @Then /^I should still see the (zone named "([^"]*)") in the list$/
     */
    public function iShouldSeeTheZoneNamedInTheList(ZoneInterface $zone)
    {
        Assert::true(
            $this->indexPage->isResourceOnPage(['code' => $zone->getCode(), 'name' => $zone->getName()]),
            sprintf('Zone named %s should exist in the registry', $zone->getName())
        );
    }

    /**
     * @Then I should be notified that this zone cannot be deleted
     */
    public function iShouldBeNotifiedThatThisZoneCannotBeDeleted()
    {
        $this->notificationChecker->checkNotification('Error Cannot delete, the zone is in use.', NotificationType::failure());
    }

    /**
     * @param ZoneInterface $zone
     * @param ZoneMemberInterface $zoneMember
     *
     * @throws \InvalidArgumentException
     */
    private function assertZoneAndItsMember(ZoneInterface $zone, ZoneMemberInterface $zoneMember)
    {
        $expectedZoneValues = [
            'code' => $zone->getCode(),
            'name' => $zone->getName(),
        ];

        Assert::true(
            $this->updatePage->hasResourceValues($expectedZoneValues),
            sprintf('Zone %s is not valid', $zone->getName())
        );

        Assert::true(
            $this->updatePage->hasMember($zoneMember),
            sprintf('Zone %s has not %s zone member', $zone->getName(), $zoneMember->getCode())
        );
    }
}
