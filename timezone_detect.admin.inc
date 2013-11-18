<?php

/**
 * @file
 * Administration pages for Timezone Detect module.
 */

/**
 * Administration form.
 *
 * @todo:
 *   - Option to notify user when their timezone is set automatically.
 *   - Option to ASK user *before* their timezone gets set automatically.
 *   - Option to use cdnjs for jstz library, rather than local file.
 *   - Option to set $SESSION timezone for anonymouse users? See https://drupal.org/node/1985906.
 */
function timezone_detect_admin_form($form, &$form_state) {

  $options = array(
    TIMEZONE_DETECT_MODE_DEFAULT => t("Set timezone on login only if it is not yet set (recommended)"),
    TIMEZONE_DETECT_MODE_LOGIN => t("Update timezone on every login"),
    TIMEZONE_DETECT_MODE_ALWAYS => t("Update timezone whenever it changes"),
  );
  $form['timezone_detect_mode'] = array(
    '#type' => 'radios',
    '#title' => t("When to set a user's timezone automatically"),
    '#default_value' => variable_get('timezone_detect_mode', TIMEZONE_DETECT_MODE_DEFAULT),
    '#options' => $options,
    '#description' => t("By default, Timezone Detect sets a user's timezone on login if it is not yet set. Alternatively, you can have the module update the user's timezone automatically on every login or whenever their timezone changes; be aware that these later settings will overwrite any manual timezone selection that the user may make."),
  );

  return system_settings_form($form);
}
