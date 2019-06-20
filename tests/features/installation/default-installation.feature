@installation @english
Feature: Test the installation process.
  As an Application site Builder
  I want to be able to install Greencko for a selected client
  So that I will be able to initiate the site with the default installation

  @environment
  Scenario: There is a environment file with database credentials
    Given A database host is set
    Then I save it into "host"
    Given A database password is set
    Then I save it into "password"
    Given A database username is set
    Then I save it into "username"
    Given A database port is set
    Then I save it into "port"
    Given A database name is set
    Then I save it into "database"

  @javascript @database
  Scenario: English installation
  As an Application site Builder
  I want to be able to install Greencko in English

    ## Choose language step
    Given I go to "/core/install.php"
    When I wait
    Then I should see "Choose language"
    When I select "en" from "langcode"
    Then I should not see "Translations will be downloaded"
    And I press "Save and continue"
    And I wait
    ## Verify requirements step
    ## Set up database step
    Given I should see "Database configuration"
    When I fill in "<<database>>" for "Database name"
    And I fill in "<<username>>" for "Database username"
    And I fill in "<<password>>" for "Database password"
    And I expand the details with title "Advanced options"
    And I should see "Host"
    And I fill in "<<host>>" for "Host"
    And I press "Save and continue"
    ## Install site step
    Then I should see "Installing Greencko"
    Given I wait for the batch job to finish
    ## Configure site step
    And I should see "Configure site"
    When I fill in "Site name" with "Greencko"
    And I fill in "Site email address" with "noreply@greencko.site"
    And I fill in "Username" with "webmaster"
    And I fill in "Password" with ".test12345"
    And I fill in "Confirm password" with ".test12345"
    And I fill in "Email address" with "webmaster@greencko.site"
    And I uncheck the box "Check for updates automatically"
    And I press "Save and continue"
    Given I wait for the batch job to finish
    ## Multilingual configuration step
    Then I should see "Multilingual configuration"
    When I press "Save and continue"
    And I wait for the batch job to finish
    ## Varbase components step
    Then I should see "Extra components"
    When I check the box "View Modes Inventory"
    And I check the box "Media Hero Slider"
    And I check the box "Varbase Carousels"
    And I check the box "Varbase Search"
    And I check the box "Varbase Blog"
    And I press "Assemble and install"
    ## Assemble Varbase components step
    Then I should see "Processing"
    Given I wait maximal "900" seconds for batch
    ## Greencko components step
    Then I should see "Extra components"
    When I press "Assemble and install"
    ## Assemble Greencko components
    And I wait for the batch job to finish
    ## Development tools step
    Then I should see "Development tools"
    When I check the box "Install Development Tools"
    Then I should see "Error messages to display"
    When I click radio button "All messages"
    And I check the box "Varbase Style guide"
    And I press "Continue"
    ## Assemble development tools step
    Then I should see "Processing"
    When I wait for the batch job to finish
    Then I should see "Welcome to Varbase"
