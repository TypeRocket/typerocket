<?php
/*
|--------------------------------------------------------------------------
| TypeRocket by Robojuice
|--------------------------------------------------------------------------
|
| TypeRocket is designed to work with WordPress 5.2+ and PHP 7+. You
| must initialize it exactly once. We suggest including TypeRocket
| from your theme and let plugins access TypeRocket from there.
|
| Happy coding!
|
| http://typerocket.com
|
*/

define('TR_PATH', __DIR__ );

// Define App Namespace
if(!defined('TR_APP_NAMESPACE')) {
    define('TR_APP_NAMESPACE', 'App');
}

// Auto Loader Init
if(!defined('TR_AUTO_LOADER')) {
    require __DIR__ . '/vendor/autoload.php';
} else {
    call_user_func(TR_AUTO_LOADER);
}

// Define Config
if(!defined('TR_CORE_CONFIG_PATH')) {
    define('TR_CORE_CONFIG_PATH', __DIR__ . '/config' );
}

new \TypeRocket\Core\Config( TR_CORE_CONFIG_PATH );

// Load TypeRocket
if( defined('WPINC') ) {
    ( new \TypeRocket\Core\Launcher() )->initCore();
}