<?php

/**
 * @file
 * Contains \Drupal\votingapi\Tests\VoteTestBase.
 */

namespace Drupal\votingapi\Tests;

use Drupal\Tests\UnitTestCase;

/**
 * Sets up page and article content types.
 */
abstract class VoteTestBase extends UnitTestCase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('votingapi', 'node');

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
  }
}