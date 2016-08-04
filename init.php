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
if( defined('TR_START') ) {
    die("TypeRocket was installed twice.");
}

define( 'TR_VERSION', '3.0.0' );
define( 'TR_START', microtime( true ) );

require __DIR__ . '/config.php';
require __DIR__ . '/functions.php';

/*
|--------------------------------------------------------------------------
| Require Core Classes
|--------------------------------------------------------------------------
|
| Autoload the core classes of TypeRocket.
|
*/
spl_autoload_register( function ( $class ) {
    $prefix   = 'TypeRocket\\';
    $base_dir = __DIR__ . '/src/';

    $len = strlen( $prefix );
    if (strncmp( $prefix, $class, $len ) !== 0) {
        return;
    }

    $relative_class = substr( $class, $len );

    $file = str_replace( '\\', '/', $relative_class ) . '.php';
    if (file_exists( $base_dir . $file )) {
        require $base_dir . $file;
    }
} );

/*
|--------------------------------------------------------------------------
| Require App Classes
|--------------------------------------------------------------------------
|
| Autoload the custom app classes for TypeRocket.
|
*/
spl_autoload_register( function ( $class ) {
    $prefix   = TR_APP_NAMESPACE . '\\';
    $base_dir = TR_APP_FOLDER_PATH;
    $base_dir = preg_replace('/\/$/', '', $base_dir);

    $len = strlen( $prefix );
    if (strncmp( $prefix, $class, $len ) !== 0) {
        return;
    }

    $relative_class = substr( $class, $len );

    $file = str_replace( '\\', '/', $relative_class ) . '.php';

    if (file_exists( $base_dir . '/' . $file )) {
        require $base_dir . '/' . $file;
    }
} );

/*
|--------------------------------------------------------------------------
| Loader
|--------------------------------------------------------------------------
|
| Load TypeRocket
|
*/
new \TypeRocket\Core(true);

define( 'TR_END', microtime( true ) );