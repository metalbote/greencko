# Greencko Automated Functional Testing

 A set of Gherkin Features and custom Varbase Context with custom
 step definitions, and assets, which help with the automatic testing for
 greencko project websites.


 This page can help  you to have the list all steps, which you need to run the
 Behat Gherkin Features to test a greencko website in your localhost machine.

To test greencko 8.x-6.x in the right way you will need to build greencko using
the composer.

## Create a Greencko project with [Composer](https://getcomposer.org/download/):

```
composer create-project metalbote/greencko-project:8.6.x-dev PROJECT_DIR_NAME --stability dev --no-interaction
```

--------------------------------------------------------------------------------
1. Change the base url value in behat.greencko.yml file, to the domain or the
   local virtual domain.

  base_url:  'http://localhost/testing/docroot'

--------------------------------------------------------------------------------
2. Open a new terminal window then start selenium2 at the port 4445. You can
   change the worker selenium robot server and the port number by changing the parameter.

```
    "wd_host: 127.0.0.1:4445/wd/hub"
```

  in the behat.greencko.yml file.
  or you can get the selenium stand alone server from
  http://www.seleniumhq.org/download/
  then you could run this command in the same location in the terminal

```
$ java -jar selenium-*.jar -port 4445
```

You can Install and configure selenium server to run on the selenium worker
server  by using our command.

```
$ sh ./tools/install-selenium-server/install-selenium-server-2.53.1.sh
```

--------------------------------------------------------------------------------
3. Automated testing config.

To run the automated testing with behat you will need to change the [ wd_host and base_url ] settings in the [ behat.greencko.yml ] file to go with your project configuration and the selenium server.

```
    Behat\MinkExtension:
      files_path: "%paths.base%/tests/assets/"
      goutte: ~
      selenium2:
        wd_host: 127.0.0.1:4445/wd/hub
        capabilities:
          browser: 'firefox'
          # browser: 'chrome'
          # browser: 'phantomjs'
          nativeEvents: true
      base_url: 'http://greencko.site'
      browser_name: 'firefox'
      # browser_name: 'chrome'
      # browser_name: 'phantomjs'
      javascript_session: selenium2
```

Then go to [ PROJECT_DIR_NAME/web/profiles/greencko ] in the
terminal then you could run the following command:

```
$ ../../../private/bin/behat tests/features/greencko
```
4. Run the behat command at PROJECT_DIR_NAME/web/profiles/greencko/testes

$ ../../../private/bin/behat tests/features/greencko/step2-apply-tests/01-website-base-requirements/01-01-user-registration_only-admins-login.feature

================================================================================

```
Feature: Website Base Requirements - User Registration - Only admins login
As an anonymous user
I will not be able to register as a user in the website
So that I will need a site admin or super admin to add me to the website

  Background: 
    Given I am an anonymous user
  
  @local @development @staging @production 
  Scenario: Check that create new account options does not appear for anonymous users.
     When I go to "/user"
      And I wait
     Then I should not see "Create new account"
  
  @local @development @staging @production
  Scenario: Check that only admin can create an account.
     When I go to "/user/register"
      And I wait
     Then I should see "Access denied"
  
  @local @development @staging @production
  Scenario: Verify that anonymous users cannot access admin pages.
     When I go to "/admin"
      And I wait
     Then I should see "Access denied"

3 scenarios (3 passed)
9 steps (9 passed)
0m2.21s (59.89Mb)

```
================================================================================

--------------------------------------------------------------------------------
5. Run this command with the .feature file to run the Gherkin Script in it to the installed site.

$ ../../../private/bin/behat tests/features/greencko/your-gherkin-feature.feature
$ ../../../private/bin/behat tests/features/project-name/your-gherkin-feature.feature

--------------------------------------------------------------------------------
8. Run this command to print all available step definitions

$ ../../../private/bin/behat -di

    - use -dl to just list definition expressions.
    - use -di to show definitions with extended info.
    - use -d 'needle' to find specific definitions.

 All Greencko, Varbase custom step definitions are tagged with #varbase or #greencko tag.

  Example : after a run for  bin/behat -di command.
================================================================================
``` 
  default | Then /^I should see image with the "([^"]*)" title text$/
          | #varbase : To Find an image with the title text attribute.
          | Example 1: Then I should see image with the "Flag Earth" title text
          | at `VarbaseContext::iShouldSeeImageWithTheTitleText()`

  default | Then /^I should see image with the "([^"]*)" alt text$/
          | #varbase : To Find an image with the alt text attribute.
          | Example 1: Then I should see image with the "Flag Earth" alt text
          | at `VarbaseContext::iShouldSeeImageWithTheAltText()`
```
================================================================================

 Scenarios are tagged with the Behat tags of:

* **@local = Local**
* **@development = Development server.**
* **@staging = Staging and testing server.**
* **@production = Production live server.**

   So that we only run bin/behat --tags with the right tag for the environment.

   Example:
================================================================================
```
    $ ../../../private/bin/behat --tags '@development' tests/features/greencko/
```
    Which it will run Scenarios which has got the @development tag.

================================================================================
```
    $ ../../../private/bin/behat --tags '@staging' tests/features/greencko/

    Which it will run Scenarios which has got the @staging tag.
```
================================================================================

    $ ../../../private/bin/behat --tags '@production' features/greencko/

    Which it will run Scenarios which has got the @production tag.

================================================================================

6. To see the report in HTML. Go and open this file in a browser.
    PROJECT_DIR_NAME/web/profiles/greencko/tests/reports/index.html
    You will see the latest report for latest run.

    if you want to custom a report you can add
    --format pretty --out std
    --format html  --out reports/report-$( date '+%Y-%m-%d_%H-%M-%S' )

    to format and select your output.

    Example:

    $ ../../../private/bin/behat tests/features/example.feature --format pretty --out std --format html --out reports/report-$( date '+%Y-%m-%d_%H-%M-%S' )

7. If you want to run all Gherkin Features over a new Varbase site.
    You will need to create the list of Testing users, and Add French, and Arabic
    languages to the site.

    # --------------------------------------------------------------------------
    # You can run the following command:
    # --------------------------------------------------------------------------
```
    $ ../../../private/bin/behat features/greencko/ --format pretty --out std  --format html  --out reports/report-$( date '+%Y-%m-%d_%H-%M-%S' )
```
    After that you can see the report in the PROJECT_DIR_NAME/web/profiles/greencko/tests/reports folder.

    If you want to run the test in steps, if you are not interested in the
    initialization and cleaning up after the test.

```
    $ ../../../private/bin/behat tests/features/greencko/step1-init-tests
    $ ../../../private/bin/behat tests/features/greencko/step2-apply-tests
    $ ../../../private/bin/behat tests/features/greencko/step3-cleanup-tests
```

# Advanced customized automated testing
To run the automated testing with behat you will need to change the [ wd_host and base_url ] settings in the
[ behat.greencko.yml ] file to go with your project configuration and the selenium server.

```
    Behat\MinkExtension:
      files_path: "%paths.base%/tests/assets/"
      goutte: ~
      selenium2:
        wd_host: 127.0.0.1:4445/wd/hub
        capabilities:
          browser: 'firefox'
          # browser: 'chrome'
          # browser: 'phantomjs'
          nativeEvents: true
      base_url: 'http://greencko.site'
      browser_name: 'firefox'
      # browser_name: 'chrome'
      # browser_name: 'phantomjs'
      javascript_session: selenium2
```
      
Testing scenarios are tagged with the Behat tags of:

* **@local = Local**
* **@development = Development server.**
* **@staging = Staging and testing server.**
* **@production = Production live server.**

So that we only run bin/behat --tags with the right tag for the environment.

Run the greencko check tests. Only to check, without any changes to the website.
```
cd web/profiles/greencko;
../../../private/bin/behat tests/features/greencko --tags '@check' --format pretty --out std  --format html  --out tests/reports/greencko-check-tests-report-$( date '+%Y-%m-%d_%H-%M-%S' );
```

Run the greencko full local tests. which developers could test all scenarios in their local machine environment.
```
cd web/profiles/greencko;
../../../private/bin/behat tests/features/greencko --tags '@local' --format pretty --out std  --format html  --out tests/reports/greencko-full-local-tests-report-$( date '+%Y-%m-%d_%H-%M-%S' );
```

Run the greencko full development tests. which developers could test scenarios on the website at the development environment.
```
cd web/profiles/greencko;
../../../private/bin/behat tests/features/greencko --tags '@development' --format pretty --out std  --format html  --out tests/reports/greencko-full-development-tests-report-$( date '+%Y-%m-%d_%H-%M-%S' );
```

Run the greencko full staging tests. which developers could test scenarios on the website at the staging environment.
```
cd web/profiles/greencko;
../../../private/bin/behat tests/features/greencko --tags '@staging' --format pretty --out std  --format html  --out tests/reports/greencko-full-staging-tests-report-$( date '+%Y-%m-%d_%H-%M-%S' );
```

Run the greencko full production only tests. not to change anything with test scenarios on the production environment.
```
cd web/profiles/greencko;
../../../private/bin/behat tests/features/greencko --tags '@production' --format pretty --out std  --format html  --out tests/reports/greencko-full-production-tests-report-$( date '+%Y-%m-%d_%H-%M-%S' );
```

Run the greencko full tests. init, apply, then cleanup.
```
cd web/profiles/greencko;
../../../private/bin/behat tests/features/greencko/step1-init-tests --format pretty --out std  --format html  --out tests/reports/greencko-init-tests-report-$( date '+%Y-%m-%d_%H-%M-%S' );
../../../private/bin/behat tests/features/greencko/step2-apply-tests --format pretty --out std  --format html  --out tests/reports/greencko-apply-tests-report-$( date '+%Y-%m-%d_%H-%M-%S' );
../../../private/bin/behat tests/features/greencko/step3-cleanup-tests --format pretty --out std  --format html  --out tests/reports/greencko-cleanup-tests-report-$( date '+%Y-%m-%d_%H-%M-%S' );
```

Run the greencko full tests.
```
cd web/profiles/greencko;
../../../private/bin/behat tests/features/greencko --format pretty --out std  --format html  --out tests/reports/greencko-full-tests-report-$( date '+%Y-%m-%d_%H-%M-%S' );
```

We could run behat tests with this set
Go to [ PROJECT_DIR_NAME/web/profiles/greencko ] in the terminal then you could run the following command:
```
$ ../../../private/bin/behat tests/features/greencko

```

Then you will be able to open the full report for the automated test in a web browser at the following path:
[ PROJECT_DIR_NAME/web/profiles/greencko/tests/reports ]
