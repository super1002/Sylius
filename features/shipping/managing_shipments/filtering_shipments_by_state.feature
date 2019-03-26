@managing_shipments
Feature: Filtering shipments from multiple channels by state
    In order to filtering shipments by state
    As an Administrator
    I want to browse all shipments with choosen state

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "UPS" shipping method with "$10.00" fee
        And the store has a product "Apple"
        And the store allows paying with "Cash on Delivery"
        And there is a customer "donald@duck.com" that placed an order "#00000001" in channel "united states"
        And the customer bought a single "Apple"
        And the customer "Donald Duck" addressed it to "Elm street", "90802" "Duckburg" in the "United States" with identical billing address
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And this order has already been shipped
        And the store has country "Canada"
        And the store operates on another channel named "Canada" in "CAD" currency
        And the store has a zone "Canada" with code "CA"
        And this zone has the "Canada" country member
        And the store has "FEDEX" shipping method with "$10.00" fee
        And the store allows paying with "Bank transfer"
        And the store has a product "Orange"
        And there is a customer "iron@man.com" that placed an order "#00000003" in channel "canada"
        And the customer bought a single "Orange"
        And the customer "Tony Stark" addressed it to "Rich street", "90802" "New York" in the "Canada" with identical billing address
        And the customer chose "FEDEX" shipping method with "bank transfer" payment
        And I am logged in as an administrator

    @ui
    Scenario: Filtering orders by a choosen state
        When I browse shipments
        And I choose "Shipped" as a shipment state
        And I filter
        Then I should see 1 shipments in the list
        And I should see an shipment with "#00000001" order number
        But I should not see an shipment with "#00000003" order number



