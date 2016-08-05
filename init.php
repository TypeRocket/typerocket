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
define( 'TR_START', microtime( true ) );
define( 'TR_PATH', __DIR__ );
define( 'TR_APP_NAMESPACE', 'App' );

if( file_exists(__DIR__ . '/vendor/autoload.php') ) {
    require __DIR__ . '/vendor/autoload.php';
}

require __DIR__ . '/config.php';

new \TypeRocket\Config();
( new \TypeRocket\Core() )->initCore();

define( 'TR_END', microtime( true ) );