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
   * A simple user with basic node and vote permissions
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
   * @var \Drupal\votingapi\Entity\Vote
   */
  private $vote;

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

    $this->logged_user = $this->drupalCreateUser(array(
      'access content',
      'create page content',
      'edit own page content'
    ));

    $this->drupalLogin($this->logged_user);

    $title = $this->randomMachineName(8);
    $edit = array(
      'uid'      => $this->loggedInUser->id(),
      'name'     => $this->loggedInUser->getUsername(),
      'type'     => 'page',
      'title'    => $title,
      'body' => array('value' => $this->randomMachineName(16))
    );
    entity_create('node', $edit)->save();

    $this->node = $this->drupalGetNodeByTitle($title);
    $this->assertTrue($this->node, 'Basic page created for Voting API tests.');
  }

  /**
   * Test voting for users with right permissions.
   */
  public function testUserWithPermissionVoteOnANode() {
    $edit = array(
      'voted_entity_type' => 'node',
      'voted_entity_id' => $this->node->id(),
      'value' => 50,
      'tag' => self::TYPE
    );
    $this->vote = entity_create('vote', $edit)->save();
    $this->assertTrue($this->vote, 'A vote was successfully cast on a node.');
  }

  //  public function testUserWithoutPermissionVoteOnANode() {
  //    $this->createNodePage();
  //        $this->assertTrue(FALSE, 'Cast a vote on the created user.');
  //        $this->assertTrue(FALSE, 'Process results for the vote on the created user.');
  //  }
}