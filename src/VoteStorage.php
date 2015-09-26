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

}