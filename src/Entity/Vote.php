<?php

/**
 * @file
 * Contains Drupal\votingapi\Entity\Vote.
 */

namespace Drupal\votingapi\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\votingapi\VoteInterface;

/**
 * Defines the Vote entity.
 *
 * @ingroup votingapi
 *
 * @ContentEntityType(
 *   id = "vote",
 *   label = @Translation("Vote"),
 *   handlers = {
 *     "storage" = "Drupal\votingapi\VoteStorage",
 *     "views_data" = "Drupal\votingapi\Entity\VoteViewsData",
 *   },
 *   base_table = "votingapi_vote",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 * )
 */
class Vote extends ContentEntityBase implements VoteInterface {
  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

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
  public function getSource() {
    return $this->get('vote_source')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setSource($source) {
    return $this->set('vote_source', $source);
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
      ->setDefaultValue('node')
      ->setSettings(array(
        'max_length' => 64
      ))
      ->setRequired(TRUE);

    $fields['voted_entity_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Voted entity'))
      ->setDescription(t('The ID from the voted entity'))
      ->setDefaultValue(0)
      ->setRequired(TRUE);

    $fields['value'] = BaseFieldDefinition::create('float')
      ->setLabel(t('Value'))
      ->setDescription(t('The numeric value of the vote.'))
      ->setDefaultValue(0)
      ->setRequired(TRUE);

    $fields['value_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Value Type'))
      ->setSettings(array(
        'max_length' => 64
      ))
      ->setDefaultValue('percent');

    $fields['tag'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Tag'))
      ->setSettings(array(
        'max_length' => 64,
      ))
      ->setDefaultValue('vote');

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user who submitted the vote.'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback('Drupal\votingapi\Entity\Vote::getCurrentUserId');

    $fields['timestamp'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'))
      ->setDefaultValue(time());

    $fields['vote_source'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Value Type'))
      ->setDescription(t('The IP from the user who submitted the vote.'))
      ->setDefaultValueCallback('Drupal\votingapi\Entity\Vote::getCurrentIp')
      ->setSettings(array(
        'max_length' => 255
      ));

    return $fields;
  }

  /**
   * Default value callback for 'user' base field definition.
   *
   * @see ::baseFieldDefinitions()
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentUserId() {
    return \Drupal::currentUser()->id();
  }

  /**
   * Default value callback for 'user' base field definition.
   *
   * @see ::baseFieldDefinitions()
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentIp() {
    return \Drupal::request()->getClientIp();
  }
}