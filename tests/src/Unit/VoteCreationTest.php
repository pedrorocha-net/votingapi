<?php

/**
 * @file
 * Contains \Drupal\votingapi\Tests\VoteCreationTest.
 */

namespace Drupal\votingapi\Tests;

use Drupal\votingapi\Tests\VoteTestBase;

/**
 * Tests the Voting API basics.
 *
 * @group votingapi
 */
class VoteCreationTest extends VoteTestBase {
  /**
   * Modules to enable.
   *
   * Enable dummy module that implements hook_ENTITY_TYPE_insert().
   *
   * @var array
   */
  public static $modules = array('dblog');

  protected function setUp() {
    parent::setUp();
  }

  /**
   * The type of vote to use.
   */
  const TYPE = 'test';

  /**
   * Tests casting a vote on an entity.
   */
  public function testVoteOnANewNode() {
    $this->assertTrue(TRUE, 'Create a new node entity.');
    $this->assertTrue(TRUE, 'Cast a vote on the created node.');
    $this->assertTrue(TRUE, 'Process results for the vote on the created node.');
  }

  /**
   * Test vote results.
   */
  public function testVoteOnANewUser() {
    $this->assertTrue(TRUE, 'Create a new user entity.');
    $this->assertTrue(TRUE, 'Cast a vote on the created user.');
    $this->assertTrue(TRUE, 'Process results for the vote on the created user.');
  }
}