<?php
namespace TypeRocket\Core;

// Run TypeRocket only once
if( defined('TR_PATH') )
    return;

// Define TypeRocket root path
define('TR_PATH', __DIR__ );

// Define TypeRocket alternate path
if( !defined('TR_ALT_PATH') )
    define('TR_ALT_PATH', TR_PATH);

// Define TypeRocket alternate path
if( !defined('TR_APP_ROOT_PATH') )
    define('TR_APP_ROOT_PATH', TR_ALT_PATH);

// Define App Namespace
if( !defined('TR_APP_NAMESPACE') )
    define('TR_APP_NAMESPACE', 'App');

// Define App auto loader
if( !defined('TR_AUTOLOAD_APP') )
    define('TR_AUTOLOAD_APP', ['prefix' => TR_APP_NAMESPACE . '\\', 'folder' => __DIR__ . '/app/']);

// Run vendor autoloader
if( !defined('TR_AUTO_LOADER') )
    require __DIR__ . '/vendor/autoload.php';
else
    call_user_func(TR_AUTO_LOADER);

// Run app autoloader
$tr_autoload_map = TR_AUTOLOAD_APP;
tr_autoload_psr4($tr_autoload_map);

// Define configuration path
if( !defined('TR_CORE_CONFIG_PATH') )
    define('TR_CORE_CONFIG_PATH', __DIR__ . '/config' );

// Boot container
( new Container )->boot();

// Load TypeRocket
if (defined('WPINC')) {
    (new System)->boot();
}