<?php
/*
|--------------------------------------------------------------------------
| TypeRocket by Robojuice
|--------------------------------------------------------------------------
|
| TypeRocket is designed to work with WordPress 3.8+ and PHP 5.3+. You
| must initialize it exactly once. DO NOT require it from a PLUGIN.
| Require TypeRocket from your THEME and let plugins use the theme's
| version.
|
| Happy coding!
|
| http://typerocket.com
|
|
|--------------------------------------------------------------------------
| Require TypeRocket
|--------------------------------------------------------------------------
|
| Try to start the TypeRocket. Need WordPress 3.8+ and PHP 5.3+
|
*/
global $wp_version;

if($wp_version < '3.8' || is_null($wp_version) ) :
  die("You need version 3.8+ of WordPress. Using " . $wp_version);
elseif(PHP_VERSION < '5.3') :
  die("You need version 5.3+ of PHP. Using " . PHP_VERSION);
elseif(!defined('TR_START')) :
  require __DIR__ . '/bootstrap/start.php';
endif;