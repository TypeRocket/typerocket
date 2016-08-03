<?php
/*
|--------------------------------------------------------------------------
| TypeRocket by Robojuice
|--------------------------------------------------------------------------
|
| TypeRocket is designed to work with WordPress 4.5+ and PHP 5.5+. You
| must initialize it exactly once. We suggest including TypeRocket
| from your theme and let plugins access TypeRocket from there.
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
| Try to start the TypeRocket. Need WordPress 4.5+ and PHP 5.5+
|
*/
global $wp_version;

if(empty($wp_version) || $wp_version < '4.5'  ) :
  die("You need version 4.5+ of WordPress. Using " . $wp_version);
elseif(PHP_VERSION < '5.5') :
  die("You need version 5.5+ of PHP. Using " . PHP_VERSION);
elseif(!defined('TR_START')) :
  require __DIR__ . '/core/start.php';
endif;
