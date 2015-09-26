<?php

/**
 * @file
 * Contains \Drupal\votingapi_test\Zebra.
 */

namespace Drupal\votingapi_test\Plugin\VoteResult;

use Drupal\votingapi\VoteResultBase;

/**
 * @VoteResult(
 *   id = "zebra",
 *   label = @Translation("Zebra"),
 *   description = @Translation("A test plugin.")
 * )
 */
class Zebra extends VoteResultBase {

  /**
   * {@inheritdoc}
   */
  public function calculateResult($votes) {
    return 10101;
  }
}
