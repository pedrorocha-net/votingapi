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
   * Get votes since a determined moment
   * @return mixed
   */
  function getVotesSinceMoment();

  /**
   * @param $entity_type_id
   * @param $entity_id
   * @return boolean
   */
  function deleteVotesForDeletedEntity($entity_type_id, $entity_id);
}