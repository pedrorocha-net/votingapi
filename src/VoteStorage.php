<?php

/**
 * @file
 * Contains \Drupal\votingapi\VoteStorage.
 */

namespace Drupal\votingapi;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

/**
 * Storage class for vote entities.
 *
 * This extends the \Drupal\entity\EntityDatabaseStorage class, adding
 * required special handling for vote entities.
 */
class VoteStorage extends SqlContentEntityStorage implements VoteStorageInterface {


  /**
   * Calculate the correct timestamp for the vote-rollover.
   */
  protected function calculateWindow() {
    $window = -1;
    if ($this->uid == 0) {
      if (!empty($this->vote_source)) {
        $window = \Drupal::config('votingapi.settings')
          ->get('anonymous_window', 86400);
      }
    }
    else {
      $window = \Drupal::config('votingapi.settings')->get('user_window', -1);
    }
    if ($window >= 0) {
      $this->timestamp = REQUEST_TIME - $window;
    }
  }

  /**
   * @inheritdoc
   */
  function recalculateResults($entity_type, $entity_id, $force_calculation = FALSE) {
    // if we're operating in cron mode, and the 'force recalculation' flag is NOT set,
    // bail out. The cron run will pick up the results.

    if (\Drupal::config('votingapi.settings')
        ->get('calculation_schedule') != 'cron' || $force_calculation == TRUE
    ) {
      \Drupal::database()->delete('votingapi_cache')
        ->condition('entity_type', $entity_type)
        ->condition('entity_id', $entity_id)
        ->execute();

      // Bulk query to pull the majority of the results we care about.
      $cache = VoteStorage::get()
        ->standardResults($entity_type, $entity_id);

      // Give other modules a chance to alter the collection of votes.
      \Drupal::moduleHandler()
        ->alter('votingapi_results', $cache, $entity_type, $entity_id);

      // Now, do the caching. Woo.
      $cached = array();
      foreach ($cache as $tag => $types) {
        foreach ($types as $type => $functions) {
          foreach ($functions as $function => $value) {
            $cached[] = new VotingApi_Result(array(
              'entity_type' => $entity_type,
              'entity_id' => $entity_id,
              'value_type' => $type,
              'value' => $value,
              'tag' => $tag,
              'function' => $function,
            ));
          }
        }
      }
      VoteResult::saveMultiple($cached);

      // Give other modules a chance to act on the results of the vote totaling.
      module_invoke_all('votingapi_results', $cached, $entity_type, $entity_id);

      return $cached;
    }
  }

  //  public static $instance = NULL;
  //
  //  public static function get() {
  //    if (!self::$instance) {
  //      $class = \Drupal::state()
  //        ->get('votingapi_vote_storage', 'VotingApi_VoteStorage');
  //      self::$instance = new $class;
  //    }
  //    return self::$instance;
  //  }

  //  public function addVote(&$vote) {
  //    drupal_write_record('votingapi_vote', $vote);
  //  }

  //  public function deleteVotes($votes, $vids) {
  //    db_delete('votingapi_vote')->condition('vote_id', $vids, 'IN')->execute();
  //  }

  public function selectVotes($criteria, $limit) {
    $query = db_select('votingapi_vote')->fields('votingapi_vote');
    foreach ($criteria as $key => $value) {
      if ($key == 'timestamp') {
        $query->condition($key, $value, '>');
      }
      else {
        $query->condition($key, $value, is_array($value) ? 'IN' : '=');
      }
    }
    if (!empty($limit)) {
      $query->range(0, $limit);
    }
    $result = $query->execute();
    $result->fetchOptions['class_name'] = 'VotingApi_Vote';
    return $result->fetchAll(PDO::FETCH_CLASS);
  }

  /**
   * @inheritdoc
   */
  function standardResults($entity_type, $entity_id) {
    $cache = array();

    $sql = "SELECT v.value_type, v.tag, ";
    $sql .= "COUNT(v.value) as value_count, SUM(v.value) as value_sum  ";
    $sql .= "FROM {votingapi_vote} v ";
    $sql .= "WHERE v.entity_type = :type AND v.entity_id = :id AND v.value_type IN ('points', 'percent') ";
    $sql .= "GROUP BY v.value_type, v.tag";
    $results = db_query($sql, array(
      ':type' => $entity_type,
      ':id' => $entity_id
    ));

    foreach ($results as $result) {
      $cache[$result->tag][$result->value_type]['count'] = $result->value_count;
      $cache[$result->tag][$result->value_type]['average'] = $result->value_sum / $result->value_count;
      if ($result->value_type == 'points') {
        $cache[$result->tag][$result->value_type]['sum'] = $result->value_sum;
      }
    }

    $sql = "SELECT v.tag, v.value, v.value_type, COUNT(1) AS score ";
    $sql .= "FROM {votingapi_vote} v ";
    $sql .= "WHERE v.entity_type = :type AND v.entity_id = :id AND v.value_type = 'option' ";
    $sql .= "GROUP BY v.value, v.tag, v.value_type";
    $results = db_query($sql, array(
      ':type' => $entity_type,
      ':id' => $entity_id
    ));

    foreach ($results as $result) {
      $cache[$result->tag][$result->value_type]['option-' . $result->value] = $result->score;
    }

    return $cache;
  }

  /**
   * Save a collection of votes to the database.
   *
   * This function does most of the heavy lifting for VotingAPI the main
   * votingapi_set_votes() function, but does NOT automatically triger re-tallying
   * of results. As such, it's useful for modules that must insert their votes in
   * separate batches without triggering unecessary recalculation.
   *
   * Remember that any module calling this function implicitly assumes responsibility
   * for calling votingapi_recalculate_results() when all votes have been inserted.
   *
   * @param array $votes
   *   An array of VotingApi_Vote instances
   *
   * @return array
   *   The same votes, with vote_id keys populated.
   *
   * @see votingapi_set_votes()
   * @see votingapi_recalculate_results()
   */
  //  public static function saveMultiple(&$votes) {
  //    if (is_object($votes)) {
  //      $votes = array($votes);
  //    }
  //    foreach ($votes as $key => $vote) {
  //      $vote->save();
  //    }
  //    module_invoke_all('votingapi_insert', $votes);
  //    return $votes;
  //  }

  /**
   * Delete votes from the database.
   *
   * @param array $votes
   *   An array of votes to delete. Each vote must have the 'vote_id' key set.
   */
  //  public static function deleteMultiple($votes = array()) {
  //    if (!empty($votes)) {
  //      module_invoke_all('votingapi_delete', $votes);
  //      $vids = array();
  //      foreach ($votes as $vote) {
  //        $vids[] = $vote->vote_id;
  //      }
  //      VoteStorage::get()->deleteVotes($votes, $vids);
  //    }
  //  }

  //  public function save() {
  //    VotingApi_VoteStorage::get()->addVote($this);
  //  }

  //  public function __construct(array $values, $entity_type) {
  //    parent::__construct($values, $entity_type);
  //
  //    if ($this->uid != 0) {
  //      $this->vote_source = NULL;
  //    }
  //
  //    $this->calculateWindow();
  //  }

  /**
   * Select individual votes from the database.
   *
   * @param int $limit
   *   An optional integer specifying the maximum number of votes to return.
   *
   * @return
   *   An array of votes matching the criteria.
   */
  //  public function select($limit = 0) {
  //    return VoteStorage::get()->selectVotes($this, $limit);
  //  }

  /**
   * Delete all matching votes.
   */
  //  public function delete() {
  //    Vote::deleteMultiple($this->select());
  //  }

//  public static function byEntity($entity_type, $entity_ids) {
//    $class = get_called_class();
//    return new $class(array(
//      'entity_type' => $entity_type,
//      'entity_id' => $entity_ids
//    ));
//  }

  //  /**
  //   * Retrieve the value of the first vote matching the criteria.
  //   */
  //  public function singleValue() {
  //    $results = $this->select();
  //    return $results[0]->value;
  //  }
}