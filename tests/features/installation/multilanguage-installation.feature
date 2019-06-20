@installation @multilingual
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
  Scenario: Multilingual installation
  As an Application site Builder
  I want to be able to install Greencko with multilingual setup

    ## Choose language step
    Given I go to "/core/install.php"
    And I wait
    Then I should see "Choose language"
    When I select "de" from "langcode"
    Then I should see "Translations will be downloaded"
    When I press "Save and continue"
    Given I wait
    ## Verify requirements step
    ## Set up database step
    Then I should see "Datenbankkonfiguration"
    When I fill in "<<database>>" for "Datenbankname"
    And I fill in "<<username>>" for "Datenbankbenutzer"
    And I fill in "<<password>>" for "Datenbankpasswort"
    And I expand the details with title "Erweiterte Optionen"
    And I should see "Host"
    And I fill in "<<host>>" for "Host"
    And I fill in "<<port>>" for "Portnummer"
    And I press "Speichern und fortfahren"
    ## Install site step
    Then I should see "Greencko wird installiert"
    Given I wait maximal "900" seconds for batch
    ## Configure site step
    Then I should see "Website konfigurieren"
    When I fill in "Name der Website" with "Greencko"
    And I fill in "E-Mail-Adresse der Website" with "noreply@greencko.site"
    And I fill in "Benutzername" with "webmaster"
    And I fill in "Passwort" with ".test12345"
    And I fill in "Passwort bestätigen" with ".test12345"
    And I fill in "E-Mail-Adresse" with "webmaster@greencko.site"
    And I scroll to find "Standard-Land"
    And I select "Deutschland" from "Standard-Land"
    And I select "Berlin" from "Standardzeitzone"
    Given I uncheck the box "Automatisch nach Aktualisierungen suchen"
    Then I should not see "E-Mail-Benachrichtigungen erhalten"
    When I press "Speichern und fortfahren"
    Given I wait for the batch job to finish
    ## Multilingual configuration step
    Then I should see "Multilingual configuration"
    And I should see "German is the default language."
    When I check the box "Enable multiple languages for this site"
    Then I should see "Please select your site's other language(s)"
    When I select "en" from "multilingual_languages[]"
    And I select "fr" from "multilingual_languages[]"
    And I select "nl" from "multilingual_languages[]"
    When I press "Speichern und fortfahren"
    Given I wait for the batch job to finish
    ## Varbase components step
    Then I should see "Extra components"
    Given I check the box "View Modes Inventory"
    And I check the box "Media Hero Slider"
    And I check the box "Varbase Carousels"
    And I check the box "Varbase Search"
    And I check the box "Varbase Blog"
    When I press "Assemble and install"
    ## Assemble Varbase components step
    Then I should see "Verarbeitung läuft …"
    Given I wait maximal "1200" seconds for batch
    ## Greencko components step
    Then I should see "Extra components"
    When I press "Assemble and install"
    ## Assemble Greencko components
    Then I should see "Verarbeitung läuft …"
    Given I wait maximal "900" seconds for batch
    ## Development tools step
    Then I should see "Development tools"
    Given I check the box "Install Development Tools"
    Then I should see "Anzuzeigende Fehlermeldungen"
    When I click radio button "Alle Nachrichten"
    Given I check the box "Varbase Style guide"
    When I press "Weiter"
    ## Assemble development tools step
    Then I should see "Verarbeitung läuft …"
    Given I wait maximal "900" seconds for batch
    Then I should see "Welcome to Varbase"
