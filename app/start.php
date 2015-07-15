<?php
if ( ! function_exists( 'add_action' )) {
    exit;
}

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
define( 'TR_VERSION', '2.0' );

/*
|--------------------------------------------------------------------------
| Require Core Classes
|--------------------------------------------------------------------------
|
| Require the core classes of TypeRocket.
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

    $file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

    if (file_exists( $file )) {
        require $file;
    }
} );

/*
|--------------------------------------------------------------------------
| Configuration
|--------------------------------------------------------------------------
|
| Load configuration file.
|
*/
$tr_config_path = __DIR__ . '/../config.php';
if(file_exists($tr_config_path)) {
    require $tr_config_path;
    new \TypeRocket\Config();
    unset($tr_config_path);
} else {
    die('Add a config.php file at ' . $tr_config_path);
}

/*
|--------------------------------------------------------------------------
| Loader
|--------------------------------------------------------------------------
|
| Load TypeRocket
|
*/
require __DIR__ . '/functions.php';
require __DIR__ . '/core.php';