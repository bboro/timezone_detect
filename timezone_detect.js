/**
 * @file
 * Determine and set user's timezone on page load.
 *
 * @todo: Use Drupal.behaviors?
 */
(function ($, Drupal, drupalSettings, cookies, jstz) {

  var behavior = Drupal.behaviors.timezoneDetect = {};

  behavior.attach = function (context) {
    var $document = $(document, context).once('timezone-detect');
    if (!$document.length) {
      // Run only once.
      return;
    }
    // Determine timezone from browser client using jsTimezoneDetect library.
    var tz = jstz.determine();
    var tz_ignored = cookies.get('timezone_detect_ignore');

    if (!tz_ignored && tz.name() != drupalSettings.timezone_detect.current_timezone) {
      behavior.showDialog(tz);
    }
  }

  behavior.showDialog = function (tz) {
    var $dialog_content = $(
      `<div>${Drupal.theme('timezoneDetectDialog', tz)}</div>`,
    );
    var dialog = Drupal.dialog($dialog_content, {
      dialogClass: 'confirm-dialog',
      resizable: false,
      closeOnEscape: false,
      width:600,
      title: Drupal.t("Timezone change detected"),
      create: function () {
        // $(this).parent().find('.ui-dialog-titlebar-close').remove();
      },
      beforeClose: false,
      close: function (event) {
        $(event.target).remove();
      },
      buttons: [
        {
          text: Drupal.t('Yes'),
          click() {
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
            $(this).dialog('close');
          },
        },
        {
          text: Drupal.t("No, don't ask me again"),
          click() {
            // Post timezone to callback url via ajax.
            $.ajax({
              type: 'POST',
              url: drupalSettings.path.baseUrl + 'timezone-detect/ajax/set-cookie',
              dataType: 'json',
              data: {
                token: drupalSettings.timezone_detect.token
              }
            });
            $(this).dialog('close');
          },
        },
      ],
    }).showModal();
  }

  /**
   * Theme function for timezone detect modal dialog.
   *
   * @return {string}
   *   Markup for the node preview modal.
   */
  Drupal.theme.timezoneDetectDialog = function(tz) {
    var text = Drupal.t('Would you like to set your timezone to @tz?', {'@tz': tz.name()});
    if (drupalSettings.timezone_detect.current_timezone) {
      text = Drupal.t('Would you like to update your timezone from @existing to @tz?', {'@existing': drupalSettings.timezone_detect.current_timezone,'@tz': tz.name()});
    }

    return `<p>${text}</p><small class="description">${Drupal.t(
      'You can always update this setting from your user profile.',
    )}</small>`;
  };

})(jQuery, Drupal, window.drupalSettings, window.Cookies, window.jstz);
