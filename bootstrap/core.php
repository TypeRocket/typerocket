<?php
/*
|--------------------------------------------------------------------------
| Require Core Classes
|--------------------------------------------------------------------------
|
| Require the core classes of TypeRocket.
|
*/
spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'TypeRocket\\';

    // base directory for the namespace prefix
    $base_dir = \tr::$paths['core'] . '/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

/*
|--------------------------------------------------------------------------
| Enhance WordPress
|--------------------------------------------------------------------------
|
| Enhance WordPress with a few functions that help clean up the interface
|
*/
$tr_enhance_obj = new \TypeRocket\Enhance();
$tr_enhance_obj->run();
unset($tr_enhance_obj);

/*
|--------------------------------------------------------------------------
| Load Plugins
|--------------------------------------------------------------------------
|
| Load TypeRocket plugins.
|
*/
if(TR_PLUGINS === true) {
  $tr_plugins_obj = new \TypeRocket\Plugins();
  $tr_plugins_obj->run(\tr::$plugins);
  unset($tr_plugins_obj);
}

/*
|--------------------------------------------------------------------------
| Init WordPress Hooks
|--------------------------------------------------------------------------
|
| Add hook into WordPress to give the main functionality needed for
| TypeRocket to work.
|
*/
$crud = new TypeRocket\Crud();
add_action('save_post', array($crud, 'save_post'));
add_action('wp_insert_comment', array($crud, 'save_comment'));
add_action('edit_comment', array($crud, 'save_comment'));
add_action('edit_user_profile_update', array($crud, 'save_user'));
add_action('personal_options_update', array($crud, 'save_user'));

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
do_action('typerocket_loaded');

add_action('after_setup_theme', function() { \TypeRocket\Registry::run(); } );

define('TR_END', microtime(true));

/*
|--------------------------------------------------------------------------
| Dev
|--------------------------------------------------------------------------
|
| Runs after everything is done.
|
*/
if(TR_DEBUG === true) {
  include_once \tr::$paths['core'] . '/dev/init.php';
}