@reports
Feature: Reports
    In order to see my store statistics
    As a store owner
    I want to be able to manage reports

    Background:
        Given there are following reports configured:
            | name           | description | renderer | renderer configuration                                       | data fetcher      | data fetcher configuration                                       |
            | TableReport    | Lorem ipsum | table    | Template:SyliusReportBundle:Table:default.html.twig          | user_registration | Period:day,Start:2010-01-01,End:2010-04-01,empty_records:false   |
            | BarChartReport | Lorem ipsum | chart    | Type:bar,Template:SyliusReportBundle:Chart:default.html.twig | user_registration | Period:month,Start:2010-01-01,End:2010-04-01,empty_records:false |
        And there is default currency configured
        And there are following users:
            | email          | enabled  | created_at          |
            | beth@foo.com   | yes      | 2010-01-01 12:00:00 |
            | martha@foo.com | yes      | 2010-01-01 13:00:00 |
            | rick@foo.com   | yes      | 2010-01-02 12:00:00 |
        And I am logged in as administrator

    Scenario: Seeing created reports it the list
        Given I am on the dashboard page
         When I follow "Reports"
         Then I should see 2 reports in the list

    Scenario: Adding new report with default options
        Given I am on the report creation page
         When I fill in the following:
            | Name        | Report1           | 
            | Description | Lorem ipsum dolor |
          And I press "Create"
         Then I should be on the page of report "Report1"
          And I should see "Report has been successfully created."
          And I should see "There is no data to display"

    Scenario: Adding new report with custom end date option
        Given I am on the report creation page
         When I fill in the following:
            | Name         | Report2           |
            | Description  | Lorem ipsum dolor |
          And I select "3" from "sylius_report_dataFetcherConfiguration_end_day"
          And I press "Create" 
         Then I should be on the page of report "Report2"
          And I should see "Report has been successfully created."
          And I should see 2 results in the list

    Scenario: Adding new report with some custom options
        Given I am on the report creation page
         When I fill in the following:
            | Name         | Report3           |
            | Description  | Lorem ipsum dolor |
          And I select "3" from "sylius_report_dataFetcherConfiguration_end_day"
          And I select "month" from "Time period"
          And I press "Create" 
         Then I should be on the page of report "Report3"
          And I should see "Report has been successfully created."
          And I should see 1 results in the list

    Scenario: Accessing report details page from list
        Given I am on the report index page
         When I press "details" near "TableReport"
         Then I should be on the page of report "TableReport"
  
    Scenario: Accessing report edit form from list
        Given I am on the report index page
         When I press "edit" near "TableReport"
         Then I should be editing report with name "TableReport"

    Scenario: Accessing report with chart renderer details page
        Given I am on the report index page
         When I press "details" near "BarChartReport"
         Then I should be on the page of report "BarChartReport"
          And "canvas" should appear on the page

    Scenario: Accessing report edit form from details page
        Given I am on the page of report "TableReport"
         When I click "edit"
         Then I should be editing report with name "TableReport"

    Scenario: Updating the report
        Given I am editing report "TableReport"
         When I fill in "Name" with "TableReportEdited"
          And I fill in "Description" with "Lorem ipsum dolor"
          And I press "Save changes"
         Then I should see "Report has been successfully updated."
          And "TableReportEdited" should appear on the page
          And "Lorem ipsum dolor" should appear on the page

    Scenario: Deleting report from index page
        Given I am on the report index page
         When I press "delete" near "TableReport"
         Then I should be on the report index page
          And I should see "Report has been successfully deleted."
          And I should not see product with name "TableReport" in that list

    Scenario: Deleting report from details page
        Given I am on the page of report "TableReport"
         When I press "delete"
         Then I should be on the report index page
          And I should see "Report has been successfully deleted."
          And I should not see product with name "TableReport" in that list
          