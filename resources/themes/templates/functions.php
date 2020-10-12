<?php
/*
|--------------------------------------------------------------------------
| TypeRocket
|--------------------------------------------------------------------------
|
| When developing make sure all public assets are located in a public
| location like the /wordpress/assets/templates directory.
|
*/

// Define Theme Directory
define('THEME_DIR', get_template_directory_uri() );

$manifest = tr_manifest_cache( tr_config('paths.assets') . '/templates/mix-manifest.json', 'templates');

// Theme Assets
add_action('wp_enqueue_scripts', function() use ($manifest) {
    wp_enqueue_style( 'main-style', THEME_DIR . $manifest['/theme/theme.css'] );
    wp_enqueue_script( 'main-script', THEME_DIR . $manifest['/theme/theme.js'], [], false, true );
});

// Admin Assets
add_action('admin_enqueue_scripts', function() use ($manifest) {
    wp_enqueue_style( 'admin-style', THEME_DIR . $manifest['/admin/admin.css'] );
    wp_enqueue_script( 'admin-script', THEME_DIR . $manifest['/admin/admin.js'], [], false, true );
});

// Supports
add_theme_support( 'post-thumbnails' );
add_theme_support( 'title-tag' );
register_nav_menu( 'main', 'Main Menu' );