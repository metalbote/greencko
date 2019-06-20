Feature: Test environment.
  As a service
  I want to know if there is any environment variables set
  So that I will be able to use them across the system

  @environment
  Scenario: There is a environment file with database credentials
    Given A database hostname variable is set
    And I save it into "hostname"
    Given A database password variable is set
    And I save it into "password"
    Given A database username variable is set
    And I save it into "username"
    Given A database port variable is set
    And I save it into "port"
    Given A database variable is set
    And I save it into "database"
    Given Database credentials are available
    And I save those into "hostname,port,database,username,password"


