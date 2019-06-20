@javascript @multilingual
Feature: Website Languages - All content translatable to all languages
As a logged in user with a permission to translate content
I want to be able to check if all content types are translatable
So that I will be able to create a content then I will have the option to translate the content to other languages in the site

  @installed
  Scenario: Check if site admin can translate an existing English Basic Page
  to an German version.
    Given I am a logged in user with the "test_site_admin" user
     When I go to "/node/add/page"
      And I wait
      And I fill in "Test English Basic page" for "Title"
      And I fill in the rich text editor field "Body" with "Test English Basic page body"
      And I select "en" from "Language"
      And I press the "Save" button
      And I wait
     Then I should see "Test English Basic page"
      And I should see "Edit"
      And I should see "Test English Basic page body"
      And I should see "Translate"
     When I click "Translate"
      And I wait
     Then I should see "Translations of Test English Basic page"
      And I should see "Not translated" in the "German" row
     When I click "Add" in the "German" row
      And I wait
     Then I should see "Create German translation of Test English Basic page"
     When I fill in "Test deutsche Standardseite" for "Title"
      And I fill in the rich text editor field "Body" with "Test Textkörper der deutschen Standardseite"
      And I press the "op" button
      And I wait
     Then I should see "Test deutsche Standardseite"
     When I click "Translate"
      And I wait
     Then I should see "Test English Basic page"
