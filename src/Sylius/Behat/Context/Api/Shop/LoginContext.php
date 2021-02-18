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

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ApiSecurityClientInterface;
use Sylius\Behat\Client\Request;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Webmozart\Assert\Assert;

final class LoginContext implements Context
{
    /** @var ApiSecurityClientInterface */
    private $client;

    /** @var ApiClientInterface */
    private $apiClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var Request */
    private $request;

    public function __construct(
        ApiSecurityClientInterface $client,
        ApiClientInterface $apiClient,
        ResponseCheckerInterface $responseChecker
    ) {
        $this->client = $client;
        $this->apiClient = $apiClient;
        $this->responseChecker = $responseChecker;
    }

    /**
     * @Given there is the visitor
     */
    public function iAmAVisitor(): void
    {
        // Intentionally left blank;
    }

    /**
     * @When I want to log in
     */
    public function iWantToLogIn(): void
    {
        $this->client->prepareLoginRequest();
    }

    /**
     * @When I want to reset password
     */
    public function iWantToResetPassword(): void
    {
        $this->request = Request::create('shop', 'password-reset-request', 'Bearer');
    }

    /**
     * @When I reset password for email :email in :localeCode locale
     */
    public function iResetPasswordForEmailInLocale(string $email, string $localeCode): void
    {
        $this->iWantToResetPassword();
        $this->iSpecifyTheEmail($email);
        $this->addLocaleCode($localeCode);
        $this->iResetIt();
    }

    /**
     * @When I reset it
     * @When I try to reset it
     */
    public function iResetIt(): void
    {
        $this->apiClient->executeCustomRequest($this->request);
    }

    /**
     * @When I specify the username as :username
     */
    public function iSpecifyTheUsername(string $username): void
    {
        $this->client->setEmail($username);
    }

    /**
     * @When I specify customer email as :email
     * @When I do not specify the email
     */
    public function iSpecifyTheEmail(string $email = ''): void
    {
        $this->request->setContent(['email' => $email]);
    }

    /**
     * @When I specify the password as :password
     */
    public function iSpecifyThePasswordAs(string $password): void
    {
        $this->client->setPassword($password);
    }

    /**
     * @When I log in
     * @When I try to log in
     */
    public function iLogIn(): void
    {
        $this->client->call();
    }

    /**
     * @When I log in as :email with :password password
     */
    public function iLogInAsWithPassword(string $email, string $password): void
    {
        $this->client->prepareLoginRequest();
        $this->client->setEmail($email);
        $this->client->setPassword($password);
        $this->client->call();
    }

    /**
     * @When I log out
     * @When the customer logged out
     */
    public function iLogOut()
    {
        $this->client->logOut();
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn(): void
    {
        Assert::true($this->client->isLoggedIn(), 'Shop user should be logged in, but they are not.');
    }

    /**
     * @Then I should not be logged in
     */
    public function iShouldNotBeLoggedIn(): void
    {
        Assert::false($this->client->isLoggedIn(), 'Shop user should not be logged in, but they are.');
    }

    /**
     * @Then I should be notified about bad credentials
     */
    public function iShouldBeNotifiedAboutBadCredentials(): void
    {
        Assert::same($this->client->getErrorMessage(), 'Invalid credentials.');
    }

    /**
     * @Then I should be notified that email with reset instruction has been sent
     */
    public function iShouldBeNotifiedThatEmailWithResetInstructionWasSent(): void
    {
        $response = $this->apiClient->getLastResponse();
        Assert::same($response->getStatusCode(), 202);
    }

    /**
     * @Then I should be notified that the :elementName is required
     */
    public function iShouldBeNotifiedThatFirstNameIsRequired(string $elementName): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->apiClient->getLastResponse()),
            sprintf('Please enter your %s.', $elementName)
        );
    }

    private function addLocaleCode(string $localeCode): void
    {
        $this->request->updateContent(['localeCode' => $localeCode]);
    }
}
