<?php

/**
 * @file
 * Contains Drupal\votingapi\Entity\VoteCache.
 */

namespace Drupal\votingapi\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\votingapi\VoteCacheInterface;

/**
 * Defines the VoteCache entity.
 *
 * @ingroup votingapi
 *
 * @ContentEntityType(
 *   id = "vote_cache",
 *   label = @Translation("Vote Cache"),
 *   handlers = {
 *     "storage" = "Drupal\votingapi\VoteCacheStorage",
 *     "views_data" = "Drupal\votingapi\Entity\VoteCacheViewsData",
 *   },
 *   base_table = "votingapi_cache",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid"
 *   },
 * )
 */
class VoteCache extends ContentEntityBase implements VoteCacheInterface {

  /**
   * {@inheritdoc}
   */
  public function getVotedEntityType() {
    return $this->get('voted_entity_type')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setVotedEntityType($name) {
    return $this->set('voted_entity_type', $name);
  }

  /**
   * {@inheritdoc}
   */
  public function getVotedEntityId() {
    return $this->get('voted_entity_id')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setVotedEntityId($id) {
    return $this->set('voted_entity_id', $id);
  }

  /**
   * {@inheritdoc}
   */
  public function getValue() {
    return $this->get('value')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($value) {
    return $this->set('value', $value);
  }

  /**
   * {@inheritdoc}
   */
  public function getValueType() {
    return $this->get('value_type')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setValueType($value_type) {
    return $this->set('value_type', $value_type);
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTag() {
    return $this->get('tag')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTag($tag) {
    return $this->set('tag', $tag);
  }

  /**
   * {@inheritdoc}
   */
  public function getFunction() {
    return $this->get('function')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setFunction($source) {
    return $this->set('function', $source);
  }

  /**
   * {@inheritdoc}
   */
  public function getTimestamp() {
    return $this->get('timestamp')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTimestamp($timestamp) {
    return $this->set('timestamp', $timestamp);
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The vote ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The vote UUID.'))
      ->setReadOnly(TRUE);

    $fields['voted_entity_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Entity Type'))
      ->setDescription(t('The type from the voted entity.'))
      ->setSettings(array(
        'max_length' => 64
      ))
      ->setRequired(TRUE);

    $fields['voted_entity_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Voted entity'))
      ->setDescription(t('The ID from the voted entity'))
      ->setRequired(TRUE);

    $fields['value'] = BaseFieldDefinition::create('float')
      ->setLabel(t('Value'))
      ->setDescription(t('The numeric value of the vote.'))
      ->setRequired(TRUE);

    $fields['value_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Value Type'))
      ->setSettings(array(
        'max_length' => 64
      ))
      ->setRequired(TRUE);

    $fields['tag'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Tag'))
      ->setSettings(array(
        'max_length' => 64,
      ))
      ->setRequired(TRUE);

    $fields['function'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Function'))
      ->setDescription(t('Function to apply to the numbers.'))
      ->setSettings(array(
        'max_length' => 50
      ))
      ->setRequired(TRUE);

    $fields['timestamp'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'))
      ->setRequired(TRUE);

    return $fields;
  }
}