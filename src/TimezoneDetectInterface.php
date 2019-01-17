<?php

namespace Drupal\timezone_detect;

/**
 * Interface TimezoneDetectInterface.
 *
 * @package Drupal\timezone_detect
 */
interface TimezoneDetectInterface {

  const LIBRARY_WEBSITE = 'http://pellepim.bitbucket.org/jstz/';
  const LIBRARY_FILENAME = 'jstz.js';
  const LIBRARY_DOWNLOAD_URL = 'https://bitbucket.org/pellepim/jstimezonedetect/raw/default/jstz.js';

  const MODE_DEFAULT = 'default';
  const MODE_LOGIN = 'login';
  const MODE_ALWAYS = 'always';

}