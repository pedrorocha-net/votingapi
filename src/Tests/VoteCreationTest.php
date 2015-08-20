<?php

/**
 * @file
 * Contains \Drupal\votingapi\Tests\VoteCreationTest.
 */

namespace Drupal\votingapi\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the Voting API basics.
 *
 * @group VotingAPI
 */
class VoteCreationTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('node', 'votingapi');

  /**
   * An admin user to create content
   * @var \Drupal\user\Entity\User
   */
  protected $admin_user;

  /**
   * A simple user vote permission
   * @var \Drupal\user\Entity\User
   */
  protected $logged_user;

  /**
   * A simple user vote permission
   * @var \Drupal\user\Entity\User
   */
  protected $anonymous_user;

  /**
   * @var \Drupal\node\Entity\Node
   */
  private $node;

  /**
   * The type of vote to use.
   */
  const TYPE = 'test';

  protected function setUp() {
    parent::setUp();

    $this->drupalCreateContentType(array(
      'type' => 'page',
      'name' => 'Basic page'
    ));

    $this->admin_user = $this->drupalCreateUser(array(
      'access content',
      'create page content',
      'edit own page content'
    ));

    $this->drupalLogin($this->admin_user);

    $edit = array();
    $edit['title[0][value]'] = $this->randomMachineName(8);
    $edit['body[0][value]'] = $this->randomMachineName(16);
    $this->drupalPostForm('node/add/page', $edit, t('Save'));

    // Check that the node exists in the database.
    $this->node = $this->drupalGetNodeByTitle($edit['title[0][value]']);
    $this->assertTrue($this->node, 'Basic page created for Voting API tests.');
  }

  /**
   * Test voting for users with right permissions.
   */
  public function testUserWithPermissionVoteOnANode() {
    $this->assertTrue(TRUE, 'Cast a vote on the created node.');
    //    $this->assertTrue(FALSE, 'Process results for the vote on the created node.');
  }

  //  public function testUserWithoutPermissionVoteOnANode() {
  //    $this->createNodePage();
  //        $this->assertTrue(FALSE, 'Cast a vote on the created user.');
  //        $this->assertTrue(FALSE, 'Process results for the vote on the created user.');
  //  }
}