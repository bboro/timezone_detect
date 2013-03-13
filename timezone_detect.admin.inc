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
    '#description' => t("By default, Timezone Detect sets a user's timezone on login if it is not yet set. Alternatively, you can have the module update the user's timezone automatically on every login; if a user has set their timezone manually, be aware that this setting will overwrite their selection."),
  );

  return system_settings_form($form);
}

/**
 * Library information form.
 */
function timezone_detect_library_info_form($form, &$form_state) {

  $form['message'] = array(
    '#markup' => t("Information about the jsTimezoneDetect library, used by Timezone Detect. These values should not normally need to be changed."),
  );
  $form['timezone_detect_library_website'] = array(
    '#type' => 'textfield',
    '#title' => t("Library website"),
    '#default_value' => variable_get('timezone_detect_library_website', TIMEZONE_DETECT_LIBRARY_WEBSITE),
    '#description' => t("This should be the home page for the library, not the download url."),
  );
  $form['timezone_detect_library_filename'] = array(
    '#type' => 'textfield',
    '#title' => t("Library filename"),
    '#default_value' => variable_get('timezone_detect_library_filename', TIMEZONE_DETECT_LIBRARY_FILENAME),
    '#description' => t("Javascript filename, with extension."),
  );
  $form['timezone_detect_library_download_url'] = array(
    '#type' => 'textfield',
    '#title' => t("Library download url"),
    '#default_value' => variable_get('timezone_detect_library_download_url', TIMEZONE_DETECT_LIBRARY_DOWNLOAD_URL),
    '#description' => t("Full download url to the library's javascript file."),
  );

  return system_settings_form($form);
}
