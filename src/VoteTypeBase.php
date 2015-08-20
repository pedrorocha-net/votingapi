<?php

/**
 * @file
 * Contains \Drupal\votingapi\VoteTypeBase.
 */

namespace Drupal\votingapi;

use Drupal\Core\Plugin\PluginBase;

abstract class VoteTypeBase extends PluginBase implements VoteTypeInterface {

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->t($this->pluginDefinition['label']);
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t($this->pluginDefinition['description']);
  }
}