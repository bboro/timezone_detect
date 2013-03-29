<?php

/**
 * @file
 * Administration pages for Timezone Detect module.
 */

/**
 * Administration form.
 */
function timezone_detect_admin_form($form, &$form_state) {

  // Update timezone on every login?
  $options = array("Set timezone on login only if it is not yet set (recommended)", "Update timezone on every login");
  $form['timezone_detect_update_timezone_every_login'] = array(
    '#type' => 'radios',
    '#title' => t("When to set a user's timezone automatically"),
    '#default_value' => variable_get('timezone_detect_update_timezone_every_login', 0),
    '#options' => $options,
    '#description' => t("By default, Timezone Detect sets a user's timezone on login if it is not yet set. Alternatively, you can have the module update the user's timezone automatically on every login; be aware that this setting will overwrite any manual timezone selection that the user may have made."),
  );

  return system_settings_form($form);
}
