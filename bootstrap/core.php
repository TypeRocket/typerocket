<?php
/*
|--------------------------------------------------------------------------
| Require Core Classes
|--------------------------------------------------------------------------
|
| Require the core classes of TypeRocket.
|
*/
function tr_autoload($class) {
  $is_tr = (strpos($class, 'tr_') === 0) ? true : false;
  $is_tr_field = (strpos($class, 'tr_field_') === 0) ? true : false;
  if($is_tr_field) {
    $field = substr($class, 9);
    include tr::$paths['core'] . "/fields/{$field}/class.php";
  }
  elseif($is_tr) {
    $class = substr($class, 3);
    include tr::$paths['core'] . '/class-' . $class . '.php';
  }
}

spl_autoload_register('tr_autoload');

/*
|--------------------------------------------------------------------------
| Enhance WordPress
|--------------------------------------------------------------------------
|
| Enhance WordPress with a few functions that help clean up the interface
|
*/
$tr_enhance_obj = new tr_enhance;
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
  include_once tr::$paths['core'] . '/class-plugins.php';
  $tr_plugins_obj = new tr_plugin();
  $tr_plugins_obj->run(tr::$plugins);
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
$crud = new tr_crud();
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

add_action('after_setup_theme', 'tr_registry::run');

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
  include_once tr::$paths['core'] . '/dev/init.php';
}