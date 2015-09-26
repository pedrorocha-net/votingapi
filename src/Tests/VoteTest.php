<?php

/**
 * @file
 * Contains \Drupal\votingapi\Tests\VoteTest.
 */

namespace Drupal\votingapi\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests the Voting API basics.
 *
 * @group voting
 */
class VoteTest extends WebTestBase {

  /**
   * The type of vote to use.
   */
  const TYPE = 'test';

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = array('node', 'votingapi', 'votingapi_test');

  /**
   * Tests casting a vote on an entity.
   */
  public function testVotes() {
    $vote_query = $this->container->get('entity.query')->get('vote');
    $vote_storage = $this->container->get('entity.manager')->getStorage('vote');
    $node = $this->drupalCreateNode();
    $user = $this->drupalCreateUser();

    // There are no votes on this entity yet
    $query = $vote_query->condition('entity_type', 'node')
      ->condition('entity_id', $node->id());
    $votes = $query->execute();
    $this->assertEqual(count($votes), 0, 'Vote count for a node is initially zero.');

    // Add a vote to a node
    $vote = $vote_storage->create(array(
      'type' => self::TYPE,
      'entity_id' => $node->id(),
      'entity_type' => 'node',
      'user' => $user,
      'value' => -1,
    ));
    $vote->save();
    $votes = $query->execute();
    $this->assertEqual(count($votes), 1, 'After a vote is cast on a node, it can be retrieved.');
    $vote = $vote_storage->load(reset($votes));
    $this->assertNotNull($vote, 'Node vote was loaded.');
    $this->assertEqual($vote->getOwnerId(), $user->id(), 'Node vote has correct user.');
    $this->assertEqual($vote->getValue(), -1, 'Node vote has correct value.');
    $this->assertNotEqual($vote->getSource(), '', 'A vote with no explicit source received the default value.');

    // Add a vote to a user
    $vote = $vote_storage->create(array(
      'type' => self::TYPE,
      'entity_id' => $user->id(),
      'entity_type' => 'user',
    ));
    $vote->save();

    $vote_query = $this->container->get('entity.query')->get('vote');
    $query = $vote_query->condition('entity_type', 'user')
      ->condition('entity_id', $user->id());
    $votes = $query->execute();
    $this->assertEqual(count($votes), 1, 'After a vote is cast on a user, it can be retrieved.');
    $vote = $vote_storage->load(reset($votes));
    $this->assertNotNull($vote, 'User vote was loaded.');
    $this->assertEqual($vote->getOwnerId(), 0, 'A vote with no explicit user received the default value.');
    $this->assertEqual($vote->getValue(), 1, 'A vote with no explicit value received the default value.');

    // Deleting entity deletes votes
    entity_delete_multiple('user', array($user->id()));
    $votes = $query->execute();
    $this->assertEqual(count($votes), 0, 'When an entity is deleted, the votes are also deleted.');
  }

  /**
   * Test vote results.
   */
  public function testVoteResults() {
    $vote_storage = $this->container->get('entity.manager')->getStorage('vote');
    $node = $this->drupalCreateNode();
    $user = $this->drupalCreateUser();
    $manager = $this->container->get('plugin.manager.votingapi.result');

    // Save a few votes so that we have data.
    $values = array(10, 20, 60);
    foreach ($values as $value) {
      $vote_storage->create(array(
        'type' => self::TYPE,
        'entity_id' => $node->id(),
        'entity_type' => 'node',
        'user' => $user,
        'value' => $value,
      ))->save();
    }

    $results = $manager->getResults('node', $node->id());

    // Standard results are available and correct.
    $this->assertFalse(empty($results['test']), 'Results for test vote type are available.');
    $this->assertTrue(isset($results['test']['vote_sum']), 'Sum was calculated.');
    $this->assertEqual($results['test']['vote_sum'], 90, 'Sum is correct.');
    $this->assertTrue(isset($results['test']['vote_average']), 'Average was calculated.');
    $this->assertEqual($results['test']['vote_average'], 30, 'Average is correct.');

    // When you remove a result type via the hook, it is not longer available.
    $this->assertTrue(empty($results['test']['vote_count']), 'Result removed via alter hook was not calculated.');

    // Contrib modules can add new result types.
    $this->assertTrue(isset($results['test']['zebra']), 'New result was calculated.');
    $this->assertEqual($results['test']['zebra'], 10101, 'New result is correct.');

    // Deleting entity removes results
    entity_delete_multiple('node', array($node->id()));
    $results = $manager->getResults('node', $node->id());
    $this->assertTrue(empty($results), 'When an entity is deleted, the voting results are also deleted.');
  }
}
