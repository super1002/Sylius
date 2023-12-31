@checkout
Feature: Preventing not available payment method selection
    In order to pay for my order properly
    As a Customer
    I want to be prevented from selecting not available payment methods

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store allows paying with "Paypal Express Checkout"
        And the store allows paying with "Bank transfer"
        And the store ships everywhere for Free
        And I am a logged in customer

    @ui @api
    Scenario: Not being able to select disabled payment method
        Given the payment method "Paypal Express Checkout" is disabled
        And I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I select "Free" shipping method
        And I complete the shipping step
        Then I should not be able to select "Paypal Express Checkout" payment method

    @ui @api
    Scenario: Not being able to select payment method not available for order channel
        Given the store has "Cash on delivery" payment method not assigned to any channel
        And I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I select "Free" shipping method
        And I complete the shipping step
        Then I should not be able to select "Cash on delivery" payment method
        And I should be able to select "Paypal Express Checkout" payment method

    @api
    Scenario: Preventing customer from selecting nonexistent payment method
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        And I select "Free" shipping method
        And I complete the shipping step
        And I try to select "Free" payment method
        Then I should be informed that payment method with code "Free" does not exist
