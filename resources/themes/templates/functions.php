<?php
/*
|--------------------------------------------------------------------------
| TypeRocket Templates
|--------------------------------------------------------------------------
|
| When developing make sure all public assets are located in a public
| location like the /wordpress/assets directory. Use the gulpfile.js
| to compile css and js to assets.
|
| Happy themes!
|
*/

// Define Theme Directory - This points to the /wordpress/assets folder
define('THEME_DIR', get_template_directory_uri() );

// Theme Assets
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style( 'main-style', THEME_DIR . '/css/theme.css' );
    wp_enqueue_script( 'main-script', THEME_DIR . '/js/theme.js', [], '1.0', true );
});

// Admin Assets
add_action('admin_enqueue_scripts', function() {
    wp_enqueue_style( 'admin-style', THEME_DIR . '/css/admin.css' );
    wp_enqueue_script( 'admin-script', THEME_DIR . '/js/admin.js', [], '1.0', true );
});