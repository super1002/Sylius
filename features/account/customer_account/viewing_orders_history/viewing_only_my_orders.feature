@customer_account
Feature: Viewing orders on my account page
    In order to follow my orders
    As a Customer
    I want to be able to track my placed orders

    Background:
        Given the store operates on a single channel in "France"
        And the store has "Angel T-Shirt" and "Green Arrow" products
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And I am a logged in customer
        And I placed an order "#00000666"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And I chose "Free" shipping method with "Cash on Delivery" payment
        And I bought a single "Angel T-Shirt"
        And there is another customer "oliver@teamarrow.com" that placed an order "#00000999"
        And the customer "Oliver Queen" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And the customer bought a single "Green Arrow"

    @ui
    Scenario: Viewing orders
        When I browse my orders
        Then I should see a single order in the list
        And this order should have "#00000666" number
