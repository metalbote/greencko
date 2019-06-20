<?php

namespace Greencko\tests\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ResponseTextException;
use Behat\MinkExtension\Context\MinkContext;
use Drupal;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Exception;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines application features from the specific context.
 */
class GreenckoContext extends RawDrupalContext implements Context, SnippetAcceptingContext, ContainerAwareInterface {

  use ContainerAwareTrait;

  /**
   * Hold all passed parameters.
   */
  protected $parameters = [];

  /**
   * Initializes context.
   *
   * @param array $parameters
   *   .
   *   Context parameters (set them up through behat.yml or behat.local.yml).
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   (optional) The service container.
   */
  public function __construct(array $parameters = NULL, ContainerInterface $container = NULL) {
    if (!is_null($container)) {
      $this->setContainer(Drupal::getContainer());
    }

    // Set the list of parameters.
    $this->parameters = $parameters;
  }

  // ===========================================================================
  //  Contexts
  // ===========================================================================
  // "See" Contexts ============================================================

  /**
   * @Then /^I should see "([^"]*)" element with the class "([^"]*)"$/
   */
  public function iShouldSeeElementWithTheClass($tag, $class) {
    $page = $this->getSession()->getPage();
    $element = $page->find('css', "$tag.$class");
    if (!$element) {
      throw new Exception(sprintf("%s element with the class %s was not found.", $tag, $class));
    }
  }

  /**
   * @Given /^I should see the "([^"]*)" table with the following <contents>:$/
   */
  public function iShouldSeeTheTableWithTheFollowingContents($class, TableNode $table) {
    $page = $this->getSession()->getPage();
    $table_element = $page->find('css', "table.$class");
    if (!$table_element) {
      throw new Exception("A table with the class $class wasn't found");
    }
    $hash = $table->getRows();
    // Iterate over each row, just so if there's an error we can supply
    // the row number, or empty values.
    foreach ($hash as $vals) {
      $xpath_fragments = [];
      foreach ($vals as $v) {
        $xpath_fragments[] = 'td//text()[contains(.,"' . $v . '") and not(ancestor::*[contains(@class, "ng-hide")])]';
      }
      $xpath = '//tr[' . implode(' and ', $xpath_fragments) . ']';
      if (!$table_element->findAll('xpath', $xpath)) {
        error_log($xpath);
        throw new Exception("Row with the following values not found: " . implode(', ', $vals));
      }
    }
  }

  /**
   * @Then /^I should see the images:$/
   */
  public function iShouldSeeTheImages(TableNode $table) {
    $page = $this->getSession()->getPage();
    $table_rows = $table->getRows();
    foreach ($table_rows as $rows) {
      $image = $page->find('xpath', "//img[contains(@src, '{$rows[0]}')]");
      if (!$image) {
        throw new Exception(sprintf('The image "%s" was not found in the page.', $rows[0]));
      }
    }
  }

  /**
   * @Then /^I should see the options "([^"]*)" under "([^"]*)"$/
   */
  public function iShouldSeeOptions($options, $container) {
    $options = explode(',', $options);
    $element = FALSE;
    $page = $this->getSession()->getPage();
    foreach ($options as $option) {
      $element = $page->find('xpath', "//select[@name='{$container}']//option[contains(.,'{$option}')]");
      if (!$element) {
        break;
      }
    }
    if (!$element) {
      throw new Exception("The option {$option} is missing.");
    }
  }

  // "Not See" Contexts ========================================================

  /**
   * @Then /^I should not see "([^"]*)" element with the class "([^"]*)"$/
   */
  public function iShouldNotSeeElementWithTheClass($tag, $class) {
    $page = $this->getSession()->getPage();
    $element = $page->find('css', "$tag.$class");
    if ($element) {
      throw new Exception(sprintf("%s element with the class %s was found.", $tag, $class));
    }
  }

  /**
   * @Then /^I should not be able to see the "([^"]*)" contextual link for
   *   "([^"]*)"$/
   */
  public function iShouldNotBeAbleToSeeTheContextualLink($linktext, $node_title) {
    $query = db_select('node', 'n')
      ->fields('n', ['nid'])
      ->fields('p', ['id', 'value'])
      ->condition('n.title', $node_title, '=');
    $query->innerJoin('og_membership', 'ogm', 'ogm.etid = n.nid');
    $query->innerJoin('purl', 'p', 'p.id = ogm.gid');
    $node_rows = $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    if (!count($node_rows)) {
      throw new Exception(sprintf("Could not find node with title of '%s'.", $node_title));
    }
    $node_row = array_pop($node_rows);
    $url = "/" . $node_row['value'] . "/node/" . $node_row['nid'];
    $this->visit($url);
    $xpath = "//div[@id='columns']//ul[contains(@class, 'contextual-links')]//a[text()='$linktext']";
    $elements = $this->getSession()->getPage()->findAll('xpath', $xpath);
    if (count($elements)) {
      throw new Exception(sprintf("%s node page contains the '%s' contextual link.", $node_title, $linktext));
    }
  }

  // "Scroll to" Contexts ======================================================

  /**
   * @When I scroll :elementId into view
   */
  public function scrollIntoView($elementId) {
    $function = <<<JS
(function(){
  var elem = document.getElementById("$elementId");
  elem.scrollIntoView(false);
})()
JS;
    try {
      $this->getSession()->executeScript($function);
    }
    catch (Exception $e) {
      throw new Exception("ScrollIntoView failed");
    }
  }

  /**
   * @When /^I scroll in the "([^"]*)" element until I find "([^"]*)"$/
   */
  public function iScrollUntil($element, $text) {
    $page = $this->getSession()->getPage();
    $driver = $this->getSession()->getDriver();
    $container = $page->find('css', $element);
    $scrolltest = "var elem = document.querySelector('$element');
      return elem.scrollHeight == elem.scrollTop + elem.clientHeight";
    if (!$container) {
      throw new Exception("The element matching '$element' was not found.");
    }
    $attempts = 0;
    while (!$driver->isVisible("//*[text() = '$text']") && !$page->getSession()
      ->evaluateScript($scrolltest) && $attempts < 20) {
      echo $attempts;
      $driver->executeScript("document.querySelector('$element').scrollTop += 100");
      usleep(100);
      $attempts++;
    }
    if (!$driver->isVisible("//*[text() = '$text']")) {
      throw new Exception("The text '$text' was not found in the '$element' element.");
    }
    elseif ($attempts == 20) {
      throw new Exception("20 attempts were made and the element is still not visible.");
    }
  }

  /**
   * @When /^I scroll to find "([^"]*)"$/
   */
  public function iScrollToFind($text) {
    $this->getSession()->executeScript("
      var result = document.evaluate('//*[.=\"$text\"]', document.body, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null);
      var elem = result.singleNodeValue;
      elem.scrollIntoView();
    ");
  }

  /**
   * @When /^I scroll to find "([^"]*)" in the "([^"]*)" element$/
   */
  public function iScrollToFindInElement($text, $selector) {
    $script = '';
    switch ($selector[0]) {
      case '/':
        // Xpath.
        $script .= 'var result = document.evaluate("' . $selector . '", document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null);';
        $script .= 'var elem = result.singleNodeValue;';
        break;

      case '.':
      case '#':
        // Css.
        $script .= 'var elem = document.querySelector("' . $selector . '");';
    }
    $script .= "var target = document.evaluate('.//*[.=\"$text\"]', elem, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;";
    $script .= "target.scrollIntoView()";
    $this->getSession()->executeScript($script);
  }

  // "Key press" Contexts ======================================================

  /**
   * Press a key.
   *
   * Press manually a key on keyboard.
   *
   * @Given /^(?|I) manually press "([^"]*)"$/
   */
  public function manuallyPress($key) {
    $script = "jQuery.event.trigger({ type : 'keypress', which : '" . $key . "' });";
    $this->getSession()->evaluateScript($script);
  }

  // "Click" Contexts ==========================================================

  /**
   * @When I click radio button :label with the id :id
   * @When I click radio button :label
   */
  public function clickRadioButton($label = '', $id = '') {
    $session = $this->getSession();

    $session->executeScript(
      "var inputs = document.getElementsByTagName('input');
        for(var i = 0; i < inputs.length; i++) {
        inputs[i].style.opacity = 1;
        inputs[i].style.left = 0;
        inputs[i].style.visibility = 'visible';
        inputs[i].style.position = 'relative';
        }
        ");

    $element = $session->getPage();

    $radiobutton = $id ? $element->findById($id) : $element->find('named', [
      'radio',
      $this->getSession()->getSelectorsHandler()->xpathLiteral($label),
    ]);
    if ($radiobutton === NULL) {
      throw new Exception(sprintf('The radio button with "%s" was not found on the page %s', $id ? $id : $label, $this->getSession()
        ->getCurrentUrl()));
    }
    $value = $radiobutton->getAttribute('value');
    $labelonpage = $radiobutton->getParent()->getText();
    if ($label !== '' && $label != $labelonpage) {
      throw new Exception(sprintf("Button with id '%s' has label '%s' instead of '%s' on the page %s", $id, $labelonpage, $label, $this->getSession()
        ->getCurrentUrl()));
    }
    $radiobutton->selectOption($value, FALSE);

  }

  /**
   * Click on the element with the provided xpath query.
   *
   * @When /^I click on the element with xpath "([^"]*)"$/
   */
  public function iClickOnTheElementWithXpath($xpath) {
    // Get the mink session.
    $session = $this->getSession();
    $element = $session->getPage()->find(
      'xpath',
      $session->getSelectorsHandler()->selectorToXpath('xpath', $xpath)
    );
    // Errors must not pass silently.
    if (NULL === $element) {
      throw new InvalidArgumentException(sprintf('Could not evaluate XPath: "%s"', $xpath));
    }
    // ok, let's click on it.
    $element->click();
  }

  /**
   * Click on the label using xpath.
   *
   * @When I click on the :label label
   */
  public function iClickOnTheLabel($label) {
    $label = str_replace("\"", "\\\"", $label);
    $xpath = '//label[text()="' . $label . '"]';
    $this->iClickOnTheElementWithXPath($xpath);
  }

  /**
   * Click some text.
   *
   * @When /^I click on the text "([^"]*)"$/
   */
  public function iClickOnTheText($text) {
    $session = $this->getSession();
    $element = $session->getPage()->find(
      'xpath',
      $session->getSelectorsHandler()
        ->selectorToXpath('xpath', '*//*[text()="' . $text . '"]')
    );
    if (NULL === $element) {
      throw new InvalidArgumentException(sprintf('Cannot find text: "%s"', $text));
    }

    $element->click();
  }

  /**
   * Click any element.
   *
   * @Given I click the :selector element
   */
  public function iClickTheElement($selector) {
    $page = $this->getSession()->getPage();
    $element = $page->find('css', $selector);

    if (empty($element)) {
      throw new Exception("No html element found for selector '{$selector}'");
    }

    $element->click();
  }

  /**
   * @When I click the xth :position element with the css :css
   */
  public function iClickTheElementWithTheCSS($position, $css) {
    $session = $this->getSession();
    $elements = $session->getPage()->findAll('css', $css);

    $count = 0;

    foreach ($elements as $element) {
      if ($count == $position) {
        // Now click the element.
        $element->click();
        return;
      }
      $count++;
    }
    throw new InvalidArgumentException(sprintf('Element not found with the css: "%s"', $css));
  }

  /**
   * @When /^I click li option "([^"]*)"$/
   *
   * @param $text
   * @throws \InvalidArgumentException
   */
  public function iClickLiOption($text) {
    $session = $this->getSession();
    $element = $session->getPage()->find(
      'xpath',
      $session->getSelectorsHandler()->selectorToXpath('xpath', '*//*[text()="' . $text . '"]')
    );

    if (NULL === $element) {
      throw new InvalidArgumentException(sprintf('Cannot find text: "%s"', $text));
    }

    $element->click();
  }

  // "Expand" Contexts =========================================================

  /**
   * Click on the details summary using xpath using xpath.
   *
   * @When I expand the details with title :title
   */
  public function iExpandTheDetailsWithTitle($title) {
    $title = str_replace("\"", "\\\"", $title);
    $xpath = '//details/summary[text()="' . $title . '"]';
    $this->iClickOnTheElementWithXPath($xpath);
  }

  // "Drag&Drop" Contexts ======================================================

  /**
   * @Given /^I drag&drop "([^"]*)" to "([^"]*)"$/
   */
  public function iDragDropTo($element, $destination) {
    $selenium = $this->getSession()->getDriver();
    $selenium->evaluateScript("jQuery('#{$element}').detach().prependTo('#{$destination}');");
  }

  // "Hover" Contexts ==========================================================

  /**
   * @When /^I hover over the element "([^"]*)"$/
   */
  public function iHoverOverTheElement($locator) {
    // Get the mink session.
    $session = $this->getSession();
    $element = $session->getPage()
    // Runs the actual query and returns the element.
      ->find('css', $locator);

    // Errors must not pass silently.
    if (NULL === $element) {
      throw new InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
    }

    // ok, let's hover it.
    $element->mouseOver();
  }

  // "Wait" Contexts ===========================================================

  /**
   * @Given /^(?:|I )wait maximal "(?P<time>\d+)" second(?:|s) for batch$/
   *
   * @throws \Behat\Behat\Tester\Exception\PendingException If timeout is reached
   */
  public function iWaitMaximalOfSecondsForBatch($time) {
    $time = $time * 1000.0;
    if ((bool) $this->getSession()
      ->evaluateScript('typeof jQuery !== "undefined"')) {
      $this->getSession()
        ->wait($time, 'jQuery("#updateprogress").length === 0');
    }
  }

  /**
   * @When I wait for :text to appear
   * @Then I should see :text appear
   *
   * @param $text
   *
   * @throws \Exception
   */
  public function iWaitForTextToAppear($text) {
    $this->spin(function (MinkContext $context) use ($text) {
      try {
        $context->assertPageContainsText($text);
        return TRUE;
      }
      catch (ResponseTextException $e) {
        // NOOP.
      }
      return FALSE;
    });
  }

  /**
   * @When I wait for :text to disappear
   * @Then I should see :text disappear
   *
   * @param $text
   *
   * @throws \Exception
   */
  public function iWaitForTextToDisappear($text) {
    $this->spin(function (MinkContext $context) use ($text) {
      try {
        $context->assertPageContainsText($text);
      }
      catch (ResponseTextException $e) {
        return TRUE;
      }
      return FALSE;
    });
  }

  /**
   * @Given /^I sleep for "([^"]*)"$/
   */
  public function iSleepFor($sec) {
    sleep($sec);
  }

  // "drush" Contexts ==========================================================

  /**
   * @When /^I clear the cache$/
   */
  public function iClearTheCache() {
    drupal_flush_all_caches();
  }

  // "database" Contexts =======================================================

  /**
   * Is the database clean?
   *
   * @Given /^The database is clean$/
   */
  public static function theDatabaseIsClean() {
    $db = Drupal::database();
    $query = $db->query("SHOW TABLES");
    $tables = [];
    if ($query) {
      while ($row = $query->fetchAssoc()) {
        $tables[] = $row['Tables_in_drupal8'];
      }
    }

    foreach ($tables as $table) {
      $db->schema()->dropTable($table);
    }
  }

  /**
   * Drop all tables.
   *
   * @Given /^I reset the database$/
   */
  public static function iResetTheDatabase() {
    $db = Drupal::database();
    $query = $db->query("SHOW TABLES");
    $tables = [];
    if ($query) {
      while ($row = $query->fetchAssoc()) {
        $tables[] = $row['Tables_in_drupal8'];
      }
    }

    foreach ($tables as $table) {
      $db->schema()->dropTable($table);
    }
  }

  // "Files" Contexts ==========================================================

  /**
   * Reset the Files folder.
   *
   * @Given /^I reset the files folder$/
   */
  public static function iResetTheFilesFolder() {
    $folder = DRUPAL_ROOT . '/sites/default';
    shell_exec("rm -rf " . $folder . "/files/*");
    shell_exec("chmod 777 " . $folder . "/settings.php");
    shell_exec("cp " . DRUPAL_ROOT . "/../.lando/settings.d8.php " . $folder . "/settings.php");
  }

  /**
   * Opens the files uploaded by a given user.
   *
   * @Then /I open and check the access of the files uploaded by
   *   "(?P<username>[^"]+)" and I expect access "(?P<access>[^"]+)"$/
   */
  public function openAndCheckFilesPrivateForUser($username, $access) {
    $allowed_access = [
      '0' => 'denied',
      '1' => 'allowed',
    ];
    if (!in_array($access, $allowed_access)) {
      throw new InvalidArgumentException(sprintf('This access option is not allowed: "%s"', $access));
    }
    $expected_access = 0;
    if ($access == 'allowed') {
      $expected_access = 1;
    }

    $query = Drupal::entityQuery('user')
      ->condition('name', $username);
    $uid = $query->execute();

    if (!empty($uid) && count($uid) === 1) {
      $uid = reset($uid);

      if ($uid) {
        $private_query = Drupal::database()->select('file_managed', 'fm');
        $private_query->addField('fm', 'fid');
        $private_query->condition('fm.uid', $uid, '=');
        $private_query->condition('fm.uri', 'private://%', 'LIKE');
        $private_files = $private_query->execute()->fetchAllAssoc('fid');

        foreach ($private_files as $fid => $file) {
          $this->openFileAndExpectAccess($fid, $expected_access);
        }
      }
    }
    else {
      throw new Exception(sprintf("User '%s' does not exist.", $username));
    }
  }

  /**
   * Checks if correct amount of uploaded files by user are private.
   *
   * @Then /User "(?P<username>[^"]+)" should have uploaded
   *   "(?P<private>[^"]+)" private files and "(?P<public>[^"]+)" public
   *   files$/
   */
  public function checkFilesPrivateForUser($username, $private, $public) {

    $query = Drupal::entityQuery('user')
      ->condition('name', $username);
    $uid = $query->execute();

    if (!empty($uid) && count($uid) === 1) {
      $uid = reset($uid);

      if ($uid) {
        $private_query = Drupal::database()->select('file_managed', 'fm');
        $private_query->addField('fm', 'fid');
        $private_query->condition('fm.uid', $uid, '=');
        $private_query->condition('fm.uri', 'private://%', 'LIKE');
        $private_count = count($private_query->execute()->fetchAllAssoc('fid'));

        $public_query = Drupal::database()->select('file_managed', 'fm');
        $public_query->addField('fm', 'fid');
        $public_query->condition('fm.uid', $uid, '=');
        $public_query->condition('fm.uri', 'public://%', 'LIKE');
        $public_count = count($public_query->execute()->fetchAllAssoc('fid'));

        PHPUnit::assertEquals($private, $private_count, sprintf("Private count was not '%s', instead '%s' private files found.", $private, $private_count));
        PHPUnit::assertEquals($public, $public_count, sprintf("Public count was not '%s', instead '%s' public files found.", $public, $public_count));
      }

    }
    else {
      throw new Exception(sprintf("User '%s' does not exist.", $username));
    }
  }

  // "Checks" Contexts ==========================================================

  /**
   * This opens the entity and check for the expected access.
   *
   * @param $entity_type
   * @param $entity_id
   * @param $expected_access
   *   0 = NO access
   *   1 = YES access
   */
  public function openEntityAndExpectAccess($entity_type, $entity_id, $expected_access) {
    $entity = entity_load($entity_type, $entity_id);
    /** @var \Drupal\Core\Url $url */
    $url = $entity->toUrl();
    $page = $url->toString();

    $this->visitPath($page);

    if ($expected_access == 0) {
      $this->assertSession()->pageTextContains('Access denied');
    }
    else {
      $this->assertSession()->pageTextNotContains('Access denied');
    }
  }

  // "wysiwyg" Contexts ==========================================================

  /**
   * Get the wysiwyg instance variable to use in Javascript.
   *
   * @param string
   *   The instanceId used by the WYSIWYG module to identify the instance.
   *
   * @return string
   *   A Javascript expression representing the WYSIWYG instance.
   *
   * @throws \Exception
   *   Throws an exception if the editor does not exist.
   */
  protected function getWysiwygInstance($instanceId) {
    $instance = "CKEDITOR.instances['$instanceId']";
    if (!$this->getSession()->evaluateScript("return !!$instance")) {
      throw new Exception(sprintf('The editor "%s" was not found on the page %s', $instanceId, $this->getSession()
        ->getCurrentUrl()));
    }
    return $instance;
  }

  /**
   * @When /^I fill in the "([^"]*)" WYSIWYG editor with "([^"]*)"$/
   */
  public function iFillInTheWysiwygEditor($instanceId, $text) {
    $instance = $this->getWysiwygInstance($instanceId);
    $this->getSession()->executeScript("$instance.setData(\"$text\");");
  }

  // ===========================================================================
  //  Helper functions
  // ===========================================================================

  /**
   * Based on Behat's own example.
   *
   * @see http://docs.behat.org/en/v2.5/cookbook/using_spin_functions.html#adding-a-timeout
   *
   * @param $lambda
   * @param int $wait
   *
   * @throws \Exception
   */
  public function spin($lambda, $wait = 60) {
    $time = time();
    $stopTime = $time + $wait;
    while (time() < $stopTime) {
      try {
        if ($lambda($this)) {
          return;
        }
      }
      catch (Exception $e) {
        // Do nothing.
      }

      usleep(250000);
    }

    throw new Exception("Spin function timed out after {$wait} seconds");
  }

}
