@checkout
Feature: Selecting order shipping method
    In order to ship my order properly
    As a Customer
    I want to be able to choose shipping method

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "Targaryen T-Shirt" priced at "$19.99"
        And the store has "Raven Post" shipping method with "€10.00" fee
        And the store has "Dragon Post" shipping method with "€30.00" fee
        And I am logged in customer

    @ui
    Scenario: Selecting one of available shipping method
        Given I have product "Targaryen T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "France" for "Jon Snow"
        When I select "Raven Post" shipping method
        And I proceed with the payment step
