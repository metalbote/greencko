<?php

namespace Greencko\tests\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Composer\Autoload\ClassLoader;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use ReflectionClass;

/**
 *
 */
class InstallationContext extends RawDrupalContext implements Context, SnippetAcceptingContext {

  protected $mysql_database;

  protected $mysql_hostname;

  protected $mysql_password;

  protected $mysql_username;

  protected $mysql_port;

  /**
   *
   */
  public function __construct() {

    $mysql_database = getenv('MYSQL_DATABASE');
    $mysql_hostname = getenv('MYSQL_HOSTNAME');
    $mysql_password = getenv('MYSQL_PASSWORD');
    $mysql_username = getenv('MYSQL_USER');
    $mysql_port = getenv('MYSQL_PORT');

    $this->mysql_database = isset($mysql_database) ? $mysql_database : 'drupal';
    $this->mysql_hostname = isset($mysql_hostname) ? $mysql_hostname : 'localhost';
    $this->mysql_password = isset($mysql_password) ? $mysql_password : 'drupal';
    $this->mysql_username = isset($mysql_username) ? $mysql_username : 'drupal';
    $this->mysql_port = isset($mysql_port) ? $mysql_port : '3306';
  }

  /**
   * @BeforeScenario @installation&&@database
   */
  public static function cleanUpBeforeInstallation() {
    $dir = self::_getVendorBinDir();
    exec($dir . '/phing drupal-uninstall');
    exec($dir . '/phing drupal-init');
    echo "Cleaned previous installation before this test";
  }

  /**
   * @Given /^A database name is set$/
   */
  public function aDatabaseNameIsSet() {
    return $this->mysql_database;
  }

  /**
   * @Given /^A database host is set$/
   */
  public function aDatabaseHostIsSet() {
    return $this->mysql_hostname;
  }

  /**
   * @Given /^A database password is set$/
   */
  public function aDatabasePasswordIsSet() {
    return $this->mysql_password;
  }

  /**
   * @Given /^A database username is set$/
   */
  public function aDatabaseUsernameIsSet() {
    return $this->mysql_username;
  }

  /**
   * @Given /^A database port is set$/
   */
  public function aDatabasePortIsSet() {
    return $this->mysql_port;
  }

  /**
   * Use phing to reset the complete drupal installation.
   *
   * @Then /^I reset the drupal installation$/
   */
  public function iResetTheDrupalInstallation() {
    $dir = self::_getVendorBinDir();
    exec($dir . '/phing drupal-reset');
    exec($dir . '/phing drupal-init');
  }

  /**
   *
   */
  private static function _getVendorBinDir() {
    $reflection = new ReflectionClass(ClassLoader::class);
    return dirname($reflection->getFileName(), 3) . '/bin';
  }

}
