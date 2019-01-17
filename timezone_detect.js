/**
 * @file
 * Determine and set user's timezone on page load.
 *
 * @todo: Use Drupal.behaviors?
 */
(function ($, Drupal, drupalSettings) {

  $(document).ready(function () {

    // Determine timezone from browser client using jsTimezoneDetect library.
    var tz = jstz.determine();

    if (tz.name() != drupalSettings.timezone_detect.current_timezone) {

      // Post timezone to callback url via ajax.
      $.ajax({
        type: 'POST',
        url: drupalSettings.path.baseUrl + 'timezone-detect/ajax/set-timezone',
        dataType: 'json',
        data: {
          timezone: tz.name(),
          token: drupalSettings.timezone_detect.token
        }
      });

      // Set any timezone select on this page to the detected timezone.
      $('select[name="timezone"] option[value="' + tz.name() + '"]')
        .closest('select')
        .val(tz.name());
    }

  });

})(jQuery, Drupal, drupalSettings);
