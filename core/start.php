<?php
if ( ! function_exists( 'add_action' )) { exit; }

/*
|--------------------------------------------------------------------------
| Time Stamp App
|--------------------------------------------------------------------------
|
| Set the app boot time to the current time in seconds since the Unix epoch
|
*/
define( 'TR_START', microtime( true ) );

/*
|--------------------------------------------------------------------------
| Version
|--------------------------------------------------------------------------
|
| Set the version for TypeRocket using the style major.minor.patch
|
*/
define( 'TR_VERSION', '3.0.0' );

/*
|--------------------------------------------------------------------------
| Configuration
|--------------------------------------------------------------------------
|
| Load configuration file.
|
*/
$tr_config_file = realpath( __DIR__ . '/../config.php' );
if( ! file_exists($tr_config_file)) {
    die('Add a the file at ' . $tr_config_file);
}

require $tr_config_file;

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
    $base_dir = __DIR__ . '/../src/';

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
new \TypeRocket\Config();
require __DIR__ . '/functions.php';
new \TypeRocket\Core(true);

define( 'TR_END', microtime( true ) );