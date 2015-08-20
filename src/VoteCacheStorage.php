<?php

/**
 * @file
 * Contains \Drupal\votingapi\VoteCacheStorage.
 */

namespace Drupal\votingapi;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

/**
 * Storage class for vote entities.
 *
 * This extends the \Drupal\entity\EntityDatabaseStorage class, adding
 * required special handling for vote entities.
 */
class VoteCacheStorage extends SqlContentEntityStorage implements VoteCacheStorageInterface {

}