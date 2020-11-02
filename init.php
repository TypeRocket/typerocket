<?php
namespace TypeRocket\Core;

// Run TypeRocket only once
if( defined('TYPEROCKET_PATH') )
    return;

// Define TypeRocket root path
define('TYPEROCKET_PATH', __DIR__ );

// Define TypeRocket alternate path
if( !defined('TYPEROCKET_ALT_PATH') )
    define('TYPEROCKET_ALT_PATH', TYPEROCKET_PATH);

// Define TypeRocket alternate path
if( !defined('TYPEROCKET_APP_ROOT_PATH') )
    define('TYPEROCKET_APP_ROOT_PATH', TYPEROCKET_ALT_PATH);

// Define App Namespace
if( !defined('TYPEROCKET_APP_NAMESPACE') )
    define('TYPEROCKET_APP_NAMESPACE', 'App');

// Define App auto loader
if( !defined('TYPEROCKET_AUTOLOAD_APP') )
    define('TYPEROCKET_AUTOLOAD_APP', ['prefix' => TYPEROCKET_APP_NAMESPACE . '\\', 'folder' => __DIR__ . '/app/']);

// Run vendor autoloader
if( !defined('TYPEROCKET_AUTO_LOADER') )
    require __DIR__ . '/vendor/autoload.php';
else
    call_user_func(TYPEROCKET_AUTO_LOADER);

// Run app autoloader
$tr_autoload_map = TYPEROCKET_AUTOLOAD_APP;
ApplicationKernel::autoloadPsr4($tr_autoload_map);

// Define configuration path
if( !defined('TYPEROCKET_CORE_CONFIG_PATH') )
    define('TYPEROCKET_CORE_CONFIG_PATH', __DIR__ . '/config' );

if( ! defined('TYPEROCKET_SKIP_INIT') )
    define('TYPEROCKET_SKIP_INIT', false );

if( ! TYPEROCKET_SKIP_INIT ) {
    ApplicationKernel::init();
}