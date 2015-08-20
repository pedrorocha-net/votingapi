<?php

/**
 * @file
 * Contains \Drupal\votingapi\VoteStorageInterface.
 */

namespace Drupal\votingapi;

use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines an interface for vote entity storage classes.
 */
interface VoteStorageInterface extends EntityStorageInterface {

  /**
   * Recalculates the aggregate results of all votes for a piece of content.
   *
   * Loads all votes for a given piece of content, then calculates and caches the
   * aggregate vote results. This is only intended for modules that have assumed
   * responsibility for the full voting cycle: the votingapi_set_vote() function
   * recalculates automatically.
   *
   * @param string $entity_type
   *   A string identifying the type of content being rated. Node, comment,
   *   aggregator item, etc.
   * @param int $entity_id
   *   The key ID of the content being rated.
   * @param boolean $force_calculation
   *   Force recalculation of results.
   *
   * @return array
   *   An array of the resulting votingapi_cache records, structured thusly:
   *   $value = $results[$ag][$value_type][$function]
   *
   * @see votingapi_set_votes()
   */
  public function recalculateResults($entity_type, $entity_id, $force_calculation = FALSE);

  public function selectVotes($criteria, $limit);

  /**
   * Builds the default VotingAPI results for the three supported voting styles.
   */
  function standardResults($entity_type, $entity_id);
}
