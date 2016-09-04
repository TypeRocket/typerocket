<?php
/*
Plugin Name: TypeRocket
Description: The WordPress Framework Designed for Developers.
Author: TypeRocket
Version: 3.0.0
Author URI: http://typerocket.com
*/
if( defined('TR_PATH') ) {
    define( 'TR_START', microtime( true ) );
    $typerocket_config = require TR_PATH . '/config/app.php';
    new \TypeRocket\Core\Config( $typerocket_config );

    $typerocket_core = new \TypeRocket\Core\Launcher();
    $typerocket_core->initCore();

    if( \TypeRocket\Core\Config::getTemplates() ) {
        $typerocket_core->overrideTemplates();
    }

    define( 'TR_END', microtime( true ) );
}