<?php

/**
 * @file
 * Contains \Drupal\votingapi\Form\AdminSettingsForm.
 */

namespace Drupal\votingapi\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configures administrative settings for VotingAPI.
 */
class AdminSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'votingapi_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('votingapi.settings');

    $options = array(
      300,
      900,
      1800,
      3600,
      10800,
      21600,
      32400,
      43200,
      86400,
      172800,
      345600,
      604800
    );
    $options = array(0 => $this->t('Immediately')) + array_combine($options, $options) + array(-1 => $this->t('Never'));
    $form['anonymous_window'] = array(
      '#type' => 'select',
      '#title' => $this->t('Anonymous vote rollover'),
      '#description' => $this->t('The amount of time that must pass before two anonymous votes from the same computer are considered unique. Setting this to \'never\' will eliminate most double-voting, but will make it impossible for multiple anonymous on the same computer (like internet cafe customers) from casting votes.'),
      '#options' => $options,
      '#default_value' => $config->get('anonymous_window'),
    );
    $form['user_window'] = array(
      '#type' => 'select',
      '#title' => $this->t('Registered user vote rollover'),
      '#description' => $this->t('The amount of time that must pass before two registered user votes from the same user ID are considered unique. Setting this to \'never\' will eliminate most double-voting for registered users.'),
      '#options' => $options,
      '#default_value' => $config->get('user_window'),
    );
    $form['calculation_schedule'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Vote tallying'),
      '#description' => $this->t('On high-traffic sites, administrators can use this setting to postpone the calculation of vote results.'),
      '#default_value' => $config->get('calculation_schedule'),
      '#options' => array(
        'immediate' => $this->t('Tally results whenever a vote is cast'),
        'cron' => $this->t('Tally results at cron-time'),
        'manual' => $this->t('Do not tally results automatically: I am using a module that manages its own vote results.')
      ),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('votingapi.settings')
      ->set('anonymous_window', $form_state->getValue('anonymous_window'))
      ->set('user_window', $form_state->getValue('user_window'))
      ->set('calculation_schedule', $form_state->getValue('calculation_schedule'))
      ->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return ['votingapi.settings'];
  }
}
