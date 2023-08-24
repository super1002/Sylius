@managing_countries
Feature: Province unique code validation
    In order to uniquely identify provinces
    As an Administrator
    I want to be prevented from adding two provinces with the same code

    Background:
        Given the store has country "United Kingdom"
        And this country has the "Northern Ireland" province with "GB-NIR" code
        And I am logged in as an administrator

    @ui @javascript @api
    Scenario: Trying to add a new province with taken code
        When I want to add a new country
        And I choose "Gibraltar"
        And I add the "Scotland" province with "GB-NIR" code
        And I try to add it
        Then I should be notified that province code must be unique

    @ui @javascript @api
    Scenario: Trying to add new provinces with duplicated codes
        When I want to edit this country
        And I add the "Scotland" province with "GB-SCO" code
        And I add the "Not Scotland" province with "GB-SCO" code
        And I try to save changes
        Then I should be notified that all province codes within this country need to be unique
        And province with name "Scotland" should not be added in this country
        And province with name "Not Scotland" should not be added in this country
