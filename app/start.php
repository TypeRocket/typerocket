<?php
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
define( 'TR_VERSION', '1.0' );

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
	$base_dir = __DIR__ . '/../core/';

	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}

	$relative_class = substr( $class, $len );

	$file = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

	if ( file_exists( $file ) ) {
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
require __DIR__ . '/../config.php';
new \TypeRocket\Config();

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