@javascript @english
Feature: Reset webmaster password after install.

  @user.reset.webmaster @english
  Scenario: Check if webmaster have to reset the password after install.
    Given I am a logged in user with the "webmaster" user
     When I go to "user/1/edit"
      And I wait
     Then I should see "webmaster"
     When I fill in ".test12345" for "Current password"
      And I fill in ".12345test" for "Password"
      And I fill in ".12345test" for "Confirm password"
      And I press "Save"
      And I wait
     Then I should see "The changes have been saved."
