jQuery(document).ready(function () {

  // Determine timezone from browser client
  var tz = jstz.determine();

  // Post timezone to callback url via ajax
  jQuery.ajax({
    type: 'POST',
    url: 'timezone-detect/set-timezone',
    dataType: 'json',
    data: 'timezone=' + tz.name()
  });

});