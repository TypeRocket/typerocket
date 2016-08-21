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
    try {
        $pdo = new \PDO('mysql:host='.DB_HOST.';dbname=' .DB_NAME. ';charset='.DB_CHARSET , DB_USER, DB_PASSWORD);
        $pdo = null;
    } catch ( \Exception $e ) {
        die("WP Error: No connection. Run 'galaxy' cli on development server." . PHP_EOL );
    }
}

if( defined('WPINC') ) {
    define( 'TR_START', microtime( true ) );
    new \TypeRocket\Core\Config( require TR_PATH . '/config/app.php' );
    ( new \TypeRocket\Core\Launcher() )->initCore();
    define( 'TR_END', microtime( true ) );
}