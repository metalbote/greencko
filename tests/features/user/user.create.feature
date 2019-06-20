@javascript @english
Feature: User Management - Standard User Management - Admins can create users and assign a role to them
  As a site admin user
  I want to be able create new user accounts and assign roles to them
  So that they will be able to use the site.

  Background:
    Given I am a logged in user with the "webmaster" user

  @init
  Scenario: Create a test.authenticated user.
    When I go to "/admin/people/create"
    And I wait
    Then I should see "Add user"
    When I fill in "test.authenticated@greencko.site" for "Email address"
    And I fill in "test.authenticated" for "Username"
    And I fill in ".12345test" for "Password"
    And I fill in ".12345test" for "Confirm password"
    And I press "Create new account"
    And I wait
    Then I should not see "The name test.authenticated is already taken."

  @init
  Scenario: Create the test.editor user.
    When I go to "/admin/people/create"
    And I wait
    Then I should see "Add user"
    When I fill in "test.editor@@greencko.site" for "Email address"
    And I fill in "test.editor" for "Username"
    And I fill in ".12345test" for "Password"
    And I fill in ".12345test" for "Confirm password"
    And I check the box "Editor"
    And I press "Create new account"
    And I wait
    Then I should not see "The name test.editor is already taken."

  @init
  Scenario: Create the test.content_admin user.
    When I go to "/admin/people/create"
    And I wait
    Then I should see "Add user"
    When I fill in "test.content_admin@@greencko.site" for "Email address"
    And I fill in "test.content_admin" for "Username"
    And I fill in ".12345test" for "Password"
    And I fill in ".12345test" for "Confirm password"
    And I check the box "Content Admin"
    And I press "Create new account"
    And I wait
    Then I should not see "The name test.content_admin is already taken."

  @init
  Scenario: Create the test.seo_admin user.
    When I go to "/admin/people/create"
    And I wait
    Then I should see "Add user"
    When I fill in "test.seo_admin@@greencko.site" for "Email address"
    And I fill in "test.seo_admin" for "Username"
    And I fill in ".12345test" for "Password"
    And I fill in ".12345test" for "Confirm password"
    And I check the box "SEO Admin"
    And I press "Create new account"
    And I wait
    Then I should not see "The name test.seo_admin is already taken."

  @init
  Scenario: Create the test.site_admin user.
    When I go to "/admin/people/create"
    And I wait
    Then I should see "Add user"
    When I fill in "test.site_admin@@greencko.site" for "Email address"
    And I fill in "test.site_admin" for "Username"
    And I fill in ".12345test" for "Password"
    And I fill in ".12345test" for "Confirm password"
    And I check the box "Site Admin"
    And I press "Create new account"
    And I wait
    Then I should not see "The name test.site_admin is already taken."

  @init
  Scenario: Create the test.super_admin user.
    When I go to "/admin/people/create"
    And I wait
    Then I should see "Add user"
    When I fill in "test.super_admin@@greencko.site" for "Email address"
    And I fill in "test.super_admin" for "Username"
    And I fill in ".12345test" for "Password"
    And I fill in ".12345test" for "Confirm password"
    And I check the box "Super Admin"
    And I press "Create new account"
    And I wait
    Then I should not see "The name test.super_admin is already taken."

  @installed
  Scenario: Check if admins can see the "Add user" button under People administration page.
    Given I go to "/admin/people"
    When I click "Add user"
    And I should see "People"
    And I should see "Username"
    And I should see "Email address"

  @installed
  Scenario: Check if admins can create a new user account as an (authenticated user).
    Given I go to "/admin/people/create"
    When I fill in "tester@greencko.site" for "Email address"
    And I fill in "Tester" for "Username"
    And I fill in ".12345test" for "Password"
    And I fill in ".12345test" for "Confirm password"
    And I press "Create new account"

  @installed @cleanup
  Scenario: Delete the Tester user.
    When I go to "/admin/people"
    And I fill in "Tester" for "Name or email contains"
    And I press "Filter"
    And I wait
    Then I should see "Tester"
    When I click "Edit" in the "Tester" row
    And I wait
    And I press "Cancel account"
    And I wait
    Then I should see "Are you sure you want to cancel the account Tester?"
    When I select the radio button "Delete the account and its content."
    And I press "Cancel account"
    And I wait 10s
    Then I should see "People"
