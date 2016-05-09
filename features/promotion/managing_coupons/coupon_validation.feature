@managing_promotion_coupons
Feature: Coupon validation
    In order to avoid making mistakes when managing a coupon
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store operates on a single channel in "France"
        And there is a promotion "Christmas sale"
        And it is coupon based promotion
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new coupon without specifying its code
        Given I want to create a new coupon for this promotion
        When I do not specify its code
        And I set its usage limit to 30
        And I set its per customer usage limit to 25
        And I make it available till "26.03.2017"
        And I try to add it
        Then I should be notified that code is required
        And I should see 0 coupon on the list related to this promotion

    @ui
    Scenario: Trying to add a new coupon with usage limit below one
        Given I want to create a new coupon for this promotion
        When I specify its code as "Santa's gift"
        And I set its usage limit to 0
        And I set its per customer usage limit to 25
        And I make it available till "26.03.2017"
        And I try to add it
        Then I should be notified that coupon usage limit must be at least one
        And I should see 0 coupon on the list related to this promotion
