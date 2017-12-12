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
*/

define('TR_APP_NAMESPACE', 'App');
define('TR_PATH', __DIR__ );
require __DIR__ . '/vendor/autoload.php';

new \TypeRocket\Core\Config( __DIR__ . '/config');

if( defined('WPINC') ) {
    ( new \TypeRocket\Core\Launcher() )->initCore();
}