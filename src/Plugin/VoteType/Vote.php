<?php

/**
 * @file
 * Contains \Drupal\votingapi\Plugin\VoteType\Vote.
 */

namespace Drupal\votingapi\Plugin\VoteType;

use Drupal\votingapi\VoteTypeBase;

/**
 * A sum of a set of votes.
 *
 * @VoteType(
 *   id = "vote",
 *   label = @Translation("Vote"),
 *   description = @Translation("Simple vote.")
 * )
 */
class Vote extends VoteTypeBase {

}