================
 INTRODUCTION
================

Original Author and Current Maintainer: Jordan Magnuson <http://drupal.org/user/269983>

Timezone Detect is a Drupal module that leverages the jsTimezoneDetect library for 
automatic detection and setting of a user's timezone via javascript. 


================
 LIMITATIONS
================

This module has all the limitations of javascript timezone detection, and the jsTimezoneDetect 
library: It will not work for users who have javascript disabled, it does not do geo-location, 
nor does it care very much about historical time zones. For more information on the limitations
of jsTimezoneDetect, see http://pellepim.bitbucket.org/jstz/ .

All of that being said, this module will generally provide pretty good "best guess" timezone 
detection.


================
 INSTALLATION
================

To use Timezone Detect, it is recommened that you also install the Libraries API module
(version 2.x), which you can download from http://drupal.org/project/libraries . Using 
Libraries API allows you to download updated versions of Timezone Detect without
having to worry about overwriting required library files.

This module requires that the jsTimezoneDetect library be downloaded and installed
in order to function. The latest version of jsTimezoneDetect can be downloaded from
https://raw.github.com/nubgames/jstimezonedetect/master/jstz.js .

If you have installed the Libraries API module version 2.x (recommended), place 
the downloaded file at sites/all/libraries/jstz/jstz.js .

If you have NOT installed the Libraries API module, you can place the downloaded file 
at sites/all/modules/timezone_detect/jstz.js . If you do this, be aware that you will
need to re-download jstz.js any time you update the Timezone Detect module.

For general instruction on how to install and update Drupal modules see
See http://drupal.org/getting-started/install-contrib .


================
 CONFIGURATION
================

This module can be configured by visiting admin/config/regional/timezone_detect .