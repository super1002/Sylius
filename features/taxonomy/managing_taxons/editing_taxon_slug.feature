@managing_taxons
Feature: Editing taxon's slug
    In order to manage access path to taxon page
    As an Administrator
    I want to be able to edit taxon's slug

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Creating a root taxon with an autogenerated slug
        When I want to create a new taxon
        And I specify its code as "MEDIEVAL_WEAPONS"
        And I name it "Medieval weapons" in "English (United States)"
        And I add it
        Then this taxon slug should be "medieval-weapons"

    @ui
    Scenario: Creating a root taxon with a custom slug
        When I want to create a new taxon
        And I specify its code as "MEDIEVAL_WEAPONS"
        And I name it "Medieval weapons" in "English (United States)"
        And I set its slug to "mw" in "English (United States)"
        And I add it
        Then this taxon slug should be "mw"

    @ui @javascript
    Scenario: Creating a taxon with an autogenerated slug for parent
        Given the store has "Medieval weapons" taxonomy
        When I want to create a new taxon for "Medieval weapons"
        And I specify its code as "SIEGE_ENGINES"
        And I name it "Siege engines" in "English (United States)"
        And I add it
        Then this taxon slug should be "medieval-weapons/siege-engines"

    @ui @javascript
    Scenario: Creating a child taxon with autogenerated slug by setting the parent
        Given the store has "Medieval weapons" taxonomy
        When I want to create a new taxon
        And I specify its code as "PIKES"
        And I name it "Pikes" in "English (United States)"
        And I set its parent taxon to "Medieval weapons"
        And I add it
        Then this taxon slug should be "medieval-weapons/pikes"

    @ui
    Scenario: Creating a taxon with a custom slug for parent
        Given the store has "Medieval weapons" taxonomy
        When I want to create a new taxon for "Medieval weapons"
        And I specify its code as "SIEGE_ENGINES"
        And I name it "Siege engines" in "English (United States)"
        And I set its slug to "medieval-weapons/siege" in "English (United States)"
        And I add it
        Then this taxon slug should be "medieval-weapons/siege"

    @ui
    Scenario: Seeing disabled slug field when editing a taxon
        Given the store has "Medieval weapons" taxonomy
        When I want to modify the "Medieval weapons" taxon
        Then the slug field should not be editable

    @ui @javascript
    Scenario: Prevent from editing the slug while changing a taxon name
        Given the store has "Medieval weapons" taxonomy
        When I want to modify the "Medieval weapons" taxon
        And I rename it to "Renaissance weapons" in "English (United States)"
        And I save my changes
        Then the slug of the "Renaissance weapons" taxon should still be "medieval-weapons"

    @ui @javascript
    Scenario: Prevent from editing the slug while setting the taxon's parent
        Given the store has "Medieval weapons" taxonomy
        And the store has "Pikes" taxonomy
        When I want to modify the "Pikes" taxon
        And I set its parent taxon to "Medieval weapons"
        And I save my changes
        Then the slug of the "Pikes" taxon should still be "pikes"

    @ui @javascript
    Scenario: Prevent from editing the slug while changing the taxon's parent
        Given the store has "Medieval weapons" taxonomy
        Given the store has "Renaissance weapons" taxonomy
        And the "Medieval weapons" taxon has child taxon "Pikes"
        When I want to modify the "Pikes" taxon
        And I change its parent taxon to "Renaissance weapons"
        And I save my changes
        Then the slug of the "Pikes" taxon should still be "medieval-weapons/pikes"

    @ui @javascript
    Scenario: Automatically changing a taxon's slug while editing a taxon's name
        Given the store has "Medieval weapons" taxonomy
        When I want to modify the "Medieval weapons" taxon
        And I enable slug modification
        And I rename it to "Renaissance weapons" in "English (United States)"
        And I save my changes
        Then the slug of the "Renaissance weapons" taxon should be "renaissance-weapons"

    @ui @javascript
    Scenario: Manually changing a taxon's slug while editing a taxon's name
        Given the store has "Medieval weapons" taxonomy
        When I want to modify the "Medieval weapons" taxon
        And I enable slug modification
        And I rename it to "Renaissance weapons" in "English (United States)"
        And I set its slug to "renaissance" in "English (United States)"
        And I save my changes
        Then the slug of the "Renaissance weapons" taxon should be "renaissance"



    @ui @javascript
    Scenario: Automatically changing a child taxon's slug when changing the parent
        Given the store has "Renaissance weapons" taxonomy
        And the store has "Medieval weapons" taxonomy
        And the "Medieval weapons" taxon has child taxon "Pikes"
        When I want to modify the "Pikes" taxon
        And I enable slug modification
        And I change its parent taxon to "Renaissance weapons"
        And I save my changes
        Then the slug of the "Pikes" taxon should be "renaissance-weapons/pikes"

    @ui @javascript
    Scenario: Manually changing a child taxon's slug when changing the parent
        Given the store has "Renaissance weapons" taxonomy
        And the store has "Medieval weapons" taxonomy
        And the "Medieval weapons" taxon has child taxon "Pikes"
        When I want to modify the "Pikes" taxon
        And I enable slug modification
        And I change its parent taxon to "Renaissance weapons"
        And I set its slug to "pikes" in "English (United States)"
        And I save my changes
        Then the slug of the "Pikes" taxon should be "pikes"
