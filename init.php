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

if(! defined('TR_PATH') ) {
    define( 'TR_PATH', __DIR__ );
}

if( file_exists( __DIR__ . '/vendor/autoload.php') ) {
    require __DIR__ . '/vendor/autoload.php';
} else {
    die('Run composer install first');
}

new \TypeRocket\Core\Config( __DIR__ . '/config');

if( defined('TR_GALAXY') ) {
    new \TypeRocket\Console\Launcher();
}

if( defined('WPINC') ) {
    define( 'TR_START', microtime( true ) );
    ( new \TypeRocket\Core\Launcher() )->initCore();
    define( 'TR_END', microtime( true ) );
}