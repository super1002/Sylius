@managing_products
Feature: Adding product with prices for multiple channels
    In order to define products for each channel
    As an Administrator
    I want to add a new product to the shop with different prices for each channel

    Background:
        Given the store has currency "USD", "GBP"
        And the store operates on a channel named "Web-US" in "USD" currency
        And the store operates on another channel named "Web-GB" in "GBP" currency
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Configure prices for each channel and currency while adding new simple product
        When I want to create a new simple product
        And I specify its code as "BOARD_DICE_BREWING"
        And I name it "Dice Brewing" in "English (United States)"
        And I set its price to "$10.00" for "Web-US" channel
        And I set its price to "£5.00" for "Web-GB" channel
        And I set its slug to "dice-brewing"
        And I add it
        Then I should be notified that it has been successfully created
        And product "Dice Brewing" should be priced at $10.00 for channel "Web-US"
        And product "Dice Brewing" should be priced at £5.00 for channel "Web-GB"
