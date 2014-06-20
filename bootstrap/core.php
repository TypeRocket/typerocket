<?php
/*
|--------------------------------------------------------------------------
| Require Core Classes
|--------------------------------------------------------------------------
|
| Require the core classes of TypeRocket.
|
*/
include_once tr::$paths['core'] . '/class-base.php';
include_once tr::$paths['core'] . '/class-utility.php';
include_once tr::$paths['core'] . '/class-validate.php';
include_once tr::$paths['core'] . '/class-list.php';
include_once tr::$paths['core'] . '/class-html.php';
include_once tr::$paths['core'] . '/class-registry.php';
include_once tr::$paths['core'] . '/class-enhance.php';
include_once tr::$paths['core'] . '/class-taxonomy.php';
include_once tr::$paths['core'] . '/class-postType.php';
include_once tr::$paths['core'] . '/class-metaBox.php';
include_once tr::$paths['core'] . '/class-field.php';
include_once tr::$paths['core'] . '/class-form.php';
include_once tr::$paths['core'] . '/class-crud.php';
include_once tr::$paths['core'] . '/class-getField.php';
include_once tr::$paths['core'] . '/class-sanitize.php';
include_once tr::$paths['core'] . '/class-layout.php';
include_once tr::$paths['core'] . '/class-icons.php';

/*
|--------------------------------------------------------------------------
| Require Core Form Field Classes
|--------------------------------------------------------------------------
|
| Require the core classes of TypeRocket form fields.
|
*/
include_once tr::$paths['core'] . '/fields/text/class.php';
include_once tr::$paths['core'] . '/fields/submit/class.php';
include_once tr::$paths['core'] . '/fields/textarea/class.php';
include_once tr::$paths['core'] . '/fields/radio/class.php';
include_once tr::$paths['core'] . '/fields/checkbox/class.php';
include_once tr::$paths['core'] . '/fields/select/class.php';
include_once tr::$paths['core'] . '/fields/editor/class.php';
include_once tr::$paths['core'] . '/fields/color/class.php';
include_once tr::$paths['core'] . '/fields/date/class.php';
include_once tr::$paths['core'] . '/fields/image/class.php';
include_once tr::$paths['core'] . '/fields/file/class.php';
include_once tr::$paths['core'] . '/fields/gallery/class.php';
include_once tr::$paths['core'] . '/fields/items/class.php';

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