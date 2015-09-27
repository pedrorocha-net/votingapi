<?php

/**
 * @file
 * Contains \Drupal\votingapi\VoteStorage.
 */

namespace Drupal\votingapi;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

/**
 * Storage class for vote entities.
 */
class VoteStorage extends SqlContentEntityStorage implements VoteStorageInterface {

  function getVotesSinceMoment() {
    $last_cron = \Drupal::state()->get('votingapi.last_cron', 0);
    return \Drupal::entityQueryAggregate('vote')
      ->condition('timestamp', $last_cron, '>')
      ->groupBy('entity_type')
      ->groupBy('entity_id')
      ->groupBy('type')
      ->execute();
  }

  function deleteVotesForDeletedEntity($entity_type_id, $entity_id) {
    $votes = \Drupal::entityQuery('vote')
      ->condition('entity_type', $entity_type_id)
      ->condition('entity_id', $entity_id)
      ->execute();
    if (!empty($votes)) {
      entity_delete_multiple('vote', $votes);
    }
    db_delete('votingapi_result')
      ->condition('entity_type', $entity_type_id)
      ->condition('entity_id', $entity_id)
      ->execute();
  }

}