<?php
/*
|--------------------------------------------------------------------------
| Time Stamp App
|--------------------------------------------------------------------------
|
| Set the app boot time to the current time in seconds since the Unix epoch
|
*/
define('TR_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Version
|--------------------------------------------------------------------------
|
| Set the version for TypeRocket using the style major.minor.patch
|
*/
define('TR_VERSION', '1.0');

/*
|--------------------------------------------------------------------------
| Configuration
|--------------------------------------------------------------------------
|
| Load configuration file.
|
*/
require __DIR__ . '/../config.php';

/*
|--------------------------------------------------------------------------
| TR Setup
|--------------------------------------------------------------------------
|
| Set the global $path variable
|
*/
class tr {
  static public $paths = null;
  static public $plugins = array();

  static function setup() {
    self::set_paths();
  }

  static function set_paths() {
    $paths = apply_filters('tr_paths', require __DIR__ . '/paths.php');
    self::set_var('paths', $paths);
  }

  static function add_plugin($string) {
    if(is_string($string)) {
      self::$plugins[] = $string;
    }
  }

  static function set_var($var, $data) {
    self::$$var = $data;
  }
}

tr::setup();

/*
|--------------------------------------------------------------------------
| Loader
|--------------------------------------------------------------------------
|
| Load TypeRocket
|
*/
require __DIR__ . '/functions.php';
require __DIR__ . '/core.php';