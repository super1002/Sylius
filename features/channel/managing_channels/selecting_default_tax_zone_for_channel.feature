@managing_channels
Feature: Selecting default tax zone for a channel
    In order to give an opportunity of choosing different default taxes logic for different channels
    As an Administrator
    I want to be able to select default tax zone

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @api @ui
    Scenario: Adding a new channel with default tax zone
        When I want to create a new channel
        And I specify its code as "MOBILE"
        And I name it "Mobile store"
        And I select the "United States" as default tax zone
        And I choose "USD" as the base currency
        And I make it available in "English (United States)"
        And I choose "English (United States)" as a default locale
        And I select the "Order items based" as tax calculation strategy
        And I add it
        Then I should be notified that it has been successfully created
        And the default tax zone for the "Mobile store" channel should be "United States"

    @api @ui
    Scenario: Selecting default tax zone for existing channel
        Given the store operates on a channel named "Web store"
        When I want to modify this channel
        And I select the "United States" as default tax zone
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the default tax zone for the "Web store" channel should be "United States"

    @api @ui
    Scenario: Removing existing channel default tax zone
        Given the store operates on a channel named "Web store"
        And its default tax zone is zone "US"
        When I want to modify this channel
        And I remove its default tax zone
        And I save my changes
        Then I should be notified that it has been successfully edited
        And channel "Web store" should not have default tax zone
