<?php

namespace Drupal\timezone_detect\Form;

/**
 * @file
 * Administration pages for Timezone Detect module.
 */

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\timezone_detect\TimezoneDetectInterface;

/**
 * Administration form.
 *
 * @todo:
 *   - Option to notify user when their timezone is set automatically.
 *   - Option to ASK user *before* their timezone gets set automatically.
 *   - Option to use cdnjs for jstz library, rather than local file.
 *   - Option to set $SESSION timezone for anonymouse users?
 *     See https://drupal.org/node/1985906.
 */
class TimezoneDetectSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'timezone_detect.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'timezone_detect_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('timezone_detect.settings');

    $options = [
      TimezoneDetectInterface::MODE_DEFAULT => $this->t("Set timezone on login only if it is not yet set (recommended)"),
      TimezoneDetectInterface::MODE_LOGIN => $this->t("Update timezone on every login"),
      TimezoneDetectInterface::MODE_ALWAYS => $this->t("Update timezone whenever it changes"),
    ];
    $form['mode'] = [
      '#type' => 'radios',
      '#title' => $this->t("When to set a user's timezone automatically"),
      '#default_value' => $config->get('mode'),
      '#options' => $options,
      '#description' => $this->t("By default, Timezone Detect sets a user's timezone on login if it is not yet set. Alternatively, you can have the module update the user's timezone automatically on every login or whenever their timezone changes; be aware that these later settings will overwrite any manual timezone selection that the user may make."),
    ];

    $form['watchdog'] = [
      '#type' => 'checkbox',
      '#title' => $this->t("Log successful events in watchdog"),
      '#default_value' => $config->get('watchdog', TRUE),
      '#description' => $this->t("By default, Timezone Detect will create a log entry every time it sets a user's timezone. This can create unnecessary noise in your log files so you are likely to want to disable this once you are confident the feature works."),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable('timezone_detect.settings')
      ->set('mode', $form_state->getValue('mode'))
      ->set('watchdog', $form_state->getValue('watchdog'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
