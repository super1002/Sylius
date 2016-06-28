@managing_customers
Feature: Seeing customer's details
    In order to see customer's details in the store
    As an Administrator
    I want to be able to show specific customer's page

    Background:
        Given I am logged in as an administrator
        And the store has customer "f.baggins@shire.me" with name "Frodo Baggins" since "2011-01-10 21:00:00"
        And he has shipping address as "Hobbiton", "Bag End", "1", "New Zealand" for "Frodo Baggins"
        And he has billing address as "Rivendell", "The Last Homely House", "7", "New Zealand" for "Bilbo Baggins"

    @ui
    Scenario: Seeing customer's basic information
        When I view details of the customer "f.baggins@shire.me"
        Then his name should be "Frodo Baggins"
        And his registration date should be "January 10, 2011 21:00"
        And his email should be "f.baggins@shire.me"

    @todo
    Scenario: Seeing customer's addresses
        When I view details of the customer "f.baggins@shire.me"
        Then his shipping address should be "Hobbiton", "Bag End", "1", "New Zealand" for "Frodo Baggins"
        And his billing address should be "Rivendell", "The Last Homely House", "7", "New Zealand" for "Bilbo Baggins"
