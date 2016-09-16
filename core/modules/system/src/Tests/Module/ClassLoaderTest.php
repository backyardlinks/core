<?php

namespace Drupal\system\Tests\Module;

use Drupal\simpletest\WebTestBase;

/**
 * Tests class loading for modules.
 *
 * @group Module
 */
class ClassLoaderTest extends WebTestBase {

  /**
   * The expected result from calling the module-provided class' method.
   */
  protected $expected = 'Drupal\\module_autoload_test\\SomeClass::testMethod() was invoked.';

  /**
   * Tests that module-provided classes can be loaded when a module is enabled.
   *
   * @see \Drupal\module_autoload_test\SomeClass
   */
  function testClassLoading() {
    // Enable the module_test and module_autoload_test modules.
    \Drupal::service('module_installer')->install(array('module_test', 'module_autoload_test'), FALSE);
    $this->resetAll();
    // Check twice to test an unprimed and primed system_list() cache.
    for ($i = 0; $i < 2; $i++) {
      $this->drupalGet('module-test/class-loading');
      $this->assertResponse(200);
      $this->assertText($this->expected, 'Autoloader loads classes from an enabled module.');
    }
  }

  /**
   * Tests that module-provided classes can't be loaded if module not installed.
   *
   * @see \Drupal\module_autoload_test\SomeClass
   */
  function testClassLoadingNotInstalledModules() {
    // Enable the module_test module.
    \Drupal::service('module_installer')->install(array('module_test'), FALSE);
    $this->resetAll();
    // Check twice to test an unprimed and primed system_list() cache.
    for ($i = 0; $i < 2; $i++) {
      $this->drupalGet('module-test/class-loading');
      $this->assertResponse(200);
      $this->assertNoText($this->expected, 'Autoloader does not load classes from a disabled module.');
    }
  }

  /**
   * Tests that module-provided classes can't be loaded from disabled modules.
   *
   * @see \Drupal\module_autoload_test\SomeClass
   */
  function testClassLoadingDisabledModules() {
    // Enable the module_test and module_autoload_test modules.
    \Drupal::service('module_installer')->install(array('module_test', 'module_autoload_test'), FALSE);
    $this->resetAll();
    // Ensure that module_autoload_test is disabled.
    $this->container->get('module_installer')->uninstall(array('module_autoload_test'), FALSE);
    $this->resetAll();
    // Check twice to test an unprimed and primed system_list() cache.
    for ($i = 0; $i < 2; $i++) {
      $this->drupalGet('module-test/class-loading');
      $this->assertResponse(200);
      $this->assertNoText($this->expected, 'Autoloader does not load classes from a disabled module.');
    }
  }

}