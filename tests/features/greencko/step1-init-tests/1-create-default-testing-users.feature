Feature: Create default testing users.

# test_authenticated: { email: 'authenticated.test@greencko.site', password: '.TZghbn56' }
# test_editor: { email: 'editor.test@greencko.site', password: '.TZghbn56' }
# test_content_admin: { email: 'content.admin.test@greencko.site', password: '.TZghbn56' }
# test_seo_admin: { email: 'seo.admin.test@greencko.site', password: '.TZghbn56' }
# test_site_admin: { email: 'site.admin.test@greencko.site', password: '.TZghbn56' }
# test_super_admin: { email: 'super.admin.test@greencko.site', password: '.TZghbn56' }

  Background:
    Given I am a logged in user with the "webmaster" user

  @init @tools @local @development @staging
  Scenario: Create the test_authenticated user.
     When I go to "/admin/people/create"
      And I wait
     Then I should see "Add user"
     When I fill in "test authenticated" for "First name"
      And I fill in "test authenticated family" for "Last name"
      And I fill in "authenticated.test@greencko.site" for "Email address"
      And I fill in "test_authenticated" for "Username"
      And I fill in ".TZghbn56" for "Password"
      And I fill in ".TZghbn56" for "Confirm password"
      And I press "Create new account"
      And I wait
     Then I should not see "The name test_authenticated is already taken."

  @init @tools @local @development @staging
  Scenario: Create the test_editor user.
     When I go to "/admin/people/create"
      And I wait
     Then I should see "Add user"
     When I fill in "test editor" for "First name"
      And I fill in "test editor family" for "Last name"
      And I fill in "editor.test@greencko.site" for "Email address"
      And I fill in "test_editor" for "Username"
      And I fill in ".TZghbn56" for "Password"
      And I fill in ".TZghbn56" for "Confirm password"
      And I check the box "Editor"
      And I press "Create new account"
      And I wait
     Then I should not see "The name test_editor is already taken."

  @init @tools @local @development @staging
  Scenario: Create the test_content_admin user.
     When I go to "/admin/people/create"
      And I wait
     Then I should see "Add user"
     When I fill in "test admin" for "First name"
      And I fill in "test admin family" for "Last name"
      And I fill in "content.admin.test@greencko.site" for "Email address"
      And I fill in "test_content_admin" for "Username"
      And I fill in ".TZghbn56" for "Password"
      And I fill in ".TZghbn56" for "Confirm password"
      And I check the box "Content Admin"
      And I press "Create new account"
      And I wait
     Then I should not see "The name test_content_admin is already taken."

  @init @tools @local @development @staging
  Scenario: Create the test_seo_admin user.
     When I go to "/admin/people/create"
      And I wait
     Then I should see "Add user"
     When I fill in "test seo admin" for "First name"
      And I fill in "test seo admin family" for "Last name"
      And I fill in "seo.admin.test@greencko.site" for "Email address"
      And I fill in "test_seo_admin" for "Username"
      And I fill in ".TZghbn56" for "Password"
      And I fill in ".TZghbn56" for "Confirm password"
      And I check the box "SEO Admin"
      And I press "Create new account"
      And I wait
     Then I should not see "The name test_seo_admin is already taken."

  @init @tools @local @development @staging
  Scenario: Create the test_site_admin user.
     When I go to "/admin/people/create"
      And I wait
     Then I should see "Add user"
     When I fill in "test site admin" for "First name"
      And I fill in "test site admin family" for "Last name"
      And I fill in "site.admin.test@greencko.site" for "Email address"
      And I fill in "test_site_admin" for "Username"
      And I fill in ".TZghbn56" for "Password"
      And I fill in ".TZghbn56" for "Confirm password"
      And I check the box "Site Admin"
      And I press "Create new account"
      And I wait
     Then I should not see "The name test_site_admin is already taken."

  @init @tools @local @development @staging
  Scenario: Create the test_super_admin user.
     When I go to "/admin/people/create"
      And I wait
     Then I should see "Add user"
     When I fill in "test super admin" for "First name"
      And I fill in "test super admin family" for "Last name"
      And I fill in "super.admin.test@greencko.site" for "Email address"
      And I fill in "test_super_admin" for "Username"
      And I fill in ".TZghbn56" for "Password"
      And I fill in ".TZghbn56" for "Confirm password"
      And I check the box "Super Admin"
      And I press "Create new account"
      And I wait
     Then I should not see "The name test_super_admin is already taken."
