<?php

namespace Greencko\tests\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Tester\Exception\PendingException;

/**
 *
 */
class ModuleContext extends RawDrupalContext implements Context, SnippetAcceptingContext {

  /**
   * @Given /^the "([^"]*)" module is enabled$/
   */
  public function theModuleIsEnabled($arg1) {
    if (!$this->invoke_code('module_enable', ['array("' . $arg1 . '")'], TRUE)) {
      throw new PendingException();
    }
  }

  /**
   * @Given /^the "([^"]*)" module is disabled$/
   */
  public function theModuleIsDisabled($arg1) {
    if (!$this->invoke_code('module_disable', ['array("' . $arg1 . '")'], TRUE)) {
      throw new PendingException();
    }
  }

  /**
   * Invoke a php code with drush.
   *
   * @param $function
   *   The function name to invoke.
   * @param $arguments
   *   Array contain the arguments for function.
   * @param $debug
   *   Set as TRUE/FALSE to display the output the function print on the
   *   screen.
   *
   * @return string $output
   */
  private function invoke_code($function, $arguments = NULL, $debug = FALSE) {
    $code = !empty($arguments) ? "$function(" . implode(',', $arguments) . ");" : "$function();";

    $output = $this->getDriver('drush')->drush("php-eval \"{$code}\"");

    if ($debug) {
      print_r($output);
    }

    return $output;
  }

}
