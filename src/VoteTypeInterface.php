<?php

/**
 * @file
 * Contains \Drupal\votingapi\VoteTypeInterface.
 */

namespace Drupal\votingapi;

/**
 * Provides an interface for a VoteType plugin.
 *
 * @see \Drupal\votingapi\Annotation\VoteType
 * @see \Drupal\votingapi\VoteTypeBase
 * @see plugin_api
 */
interface VoteTypeInterface {

  /**
   * Retrieve the label for the vote type.
   *
   * @return string
   *   The translated label
   */
  public function getLabel();


  /**
   * Retrieve the description for the vote type.
   *
   * @return string
   *   The translated description
   */
  public function getDescription();
}