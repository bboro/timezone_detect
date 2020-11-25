/**
 * @file
 * Determine and set user's timezone on page load.
 *
 * @todo: Use Drupal.behaviors?
 */
(function ($, Drupal, drupalSettings, jstz) {

  var behavior = Drupal.behaviors.timezoneDetect = {};

  behavior.attach = function () {
    // Determine timezone from browser client using jsTimezoneDetect library.
    var tz = jstz.determine();

    if (tz.name() != drupalSettings.timezone_detect.current_timezone) {
      behavior.showDialog(tz);
    }
  }

  behavior.showDialog = function (tz) {
    var dialog = Drupal.dialog(Drupal.t('Would you like to change your timezone from @existing to @current?', {'@current': tz.name(), '@existing': drupalSettings.timezone_detect.current_timezone}), {
      dialogClass: 'confirm-dialog',
      resizable: false,
      closeOnEscape: false,
      width:600,
      title:"do you want to publish this content ?",
      create: function () {
        // $(this).parent().find('.ui-dialog-titlebar-close').remove();
      },
      beforeClose: false,
      close: function (event) {
        $(event.target).remove();
      }
    });

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

})(jQuery, Drupal, window.drupalSettings, window.jstz);
