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
if( ! defined('TR_GALAXY') ) {

    define( 'TR_PATH', __DIR__ );
    define( 'TR_APP_NAMESPACE', 'App' );

    if( file_exists(__DIR__ . '/vendor/autoload.php') ) {
        require __DIR__ . '/vendor/autoload.php';
    } else {
        die('Run composer install first');
    }

} else {
    if(! $db = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)) {
        die("WP Error: No connection. Run Galaxy on development server." . PHP_EOL );
    }
    $connection = null;
}

if( defined('WPINC') ) {
    define( 'TR_START', microtime( true ) );
    new \TypeRocket\Core\Config( require TR_PATH . '/config/app.php' );
    ( new \TypeRocket\Core\Launcher() )->initCore();
    define( 'TR_END', microtime( true ) );
}