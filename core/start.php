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
define( 'TR_VERSION', '2.0.13' );

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
    /** @noinspection PhpIncludeInspection */
    require $tr_config_path;
    unset($tr_config_path);
} else {
    die('Add a config.php file at ' . $tr_config_path);
}

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
    $app = defined('TR_APP_FOLDER_PATH') ? TR_APP_FOLDER_PATH . '/' : __DIR__ . '/../app/';

    $len = strlen( $prefix );
    if (strncmp( $prefix, $class, $len ) !== 0) {
        return;
    }

    $relative_class = substr( $class, $len );

    $file = str_replace( '\\', '/', $relative_class ) . '.php';
    $app =  $app . $file;
    if (file_exists( $base_dir . $file )) {
        /** @noinspection PhpIncludeInspection */
        require $base_dir . $file;
    } elseif( file_exists( $app )) {
        /** @noinspection PhpIncludeInspection */
        require $app;
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

/*
|--------------------------------------------------------------------------
| Run Registry
|--------------------------------------------------------------------------
|
| Runs after hooks muplugins_loaded, plugins_loaded and setup_theme
| This allows the registry to work outside of the themes folder. Use
| the typerocket_loaded hook to access TypeRocket from your WP plugins.
|
*/
add_action( 'after_setup_theme', function () {
    do_action( 'typerocket_loaded' );
    \TypeRocket\Registry::initHooks();
} );

/*
|--------------------------------------------------------------------------
| Add APIs
|--------------------------------------------------------------------------
|
| Add slim REST and Matrix APIs.
|
*/
add_action('admin_init', function() {

    // Controller API
    $regex = 'typerocket_rest_api/v1/([^/]*)/?$';
    $location = 'index.php?typerocket_rest_controller=$matches[1]';
    add_rewrite_rule( $regex, $location, 'top' );

    $regex = 'typerocket_rest_api/v1/([^/]*)/([^/]*)/?$';
    $location = 'index.php?typerocket_rest_controller=$matches[1]&typerocket_rest_item=$matches[2]';
    add_rewrite_rule( $regex, $location, 'top' );

    // Matrix API
    $regex = 'typerocket_matrix_api/v1/([^/]*)/([^/]*)/?$';
    $location = 'index.php?typerocket_matrix_group=$matches[1]&typerocket_matrix_type=$matches[2]';
    add_rewrite_rule( $regex, $location, 'top' );

});

add_filter( 'query_vars', function($vars) {
    $vars[] = 'typerocket_rest_controller';
    $vars[] = 'typerocket_rest_item';
    $vars[] = 'typerocket_matrix_group';
    $vars[] = 'typerocket_matrix_type';
    return $vars;
} );

add_filter( 'template_include', function($template) {

    $resource = get_query_var('typerocket_rest_controller', null);

    $load_template = ($resource);
    $load_template = apply_filters('tr_rest_api_template', $load_template);

    if($load_template) {
        require __DIR__ . '/api/rest-v1.php';
        die();
    }

    return $template;
}, 99 );

add_filter( 'template_include', function($template) {

    $matrix_group = get_query_var('typerocket_matrix_group', null);
    $matrix_type = get_query_var('typerocket_matrix_type', null);

    $load_template = ($matrix_group && $matrix_type);
    $load_template = apply_filters('tr_matrix_api_template', $load_template);

    if($load_template) {
        require __DIR__ . '/api/matrix-v1.php';
        die();
    }

    return $template;
}, 99 );

define( 'TR_END', microtime( true ) );