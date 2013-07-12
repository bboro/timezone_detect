/**
 * @file
 * Determine and set user's timezone on page load.
 */
jQuery(document).ready(function () {

  // Determine timezone from browser client using jsTimezoneDetect library.
  var tz = jstz.determine();

  if (tz.name() != Drupal.settings.timezone_detect.current_timezone) {

    // Post timezone to callback url via ajax.
    jQuery.ajax({
      type: 'POST',
      url: '/timezone-detect/set-timezone',
      dataType: 'json',
      data: 'timezone=' + tz.name()
    });
  }

});
