<?php
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
unset( $tr_enhance_obj );

/*
|--------------------------------------------------------------------------
| Load Plugins
|--------------------------------------------------------------------------
|
| Load TypeRocket plugins.
|
*/
if ( \TypeRocket\Config::getPlugins() ) {
	$plugins_collection = new \TypeRocket\Plugin\Collection( \TypeRocket\Config::getPlugins() );
	$plugin_loader      = new \TypeRocket\Plugin\Loader( $plugins_collection );
	$plugin_loader->load();
	unset( $plugin_loader );
    unset( $plugins_collection );
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
$model_post = new TypeRocket\Models\Post();
add_action( 'save_post', array( $model_post, 'hook' ), null, 2 );

$model_comment = new TypeRocket\Models\Comment();
add_action( 'wp_insert_comment', array( $model_comment, 'hook' ) );
add_action( 'edit_comment', array( $model_comment, 'hook' ) );

$model_user = new TypeRocket\Models\User();
add_action( 'edit_user_profile_update', array( $model_user, 'hook' ) );
add_action( 'personal_options_update', array( $model_user, 'hook' ) );

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
do_action( 'typerocket_loaded' );

add_action( 'after_setup_theme', function () {
	\TypeRocket\Registry::run();
} );

/*
|--------------------------------------------------------------------------
| Add Form API
|--------------------------------------------------------------------------
|
| Add a url that will allow you to save to forms using an API. This is not
| REST but more like RPC. This API is designed to create, update and
| delete data in WordPress. Item ID's should be sent via $_POST.
|
*/
add_action('admin_init', function() {
    $regex = 'typerocket_api/v1/([^/]*)/([^/]*)/?$';
    $location = 'index.php?typerocket_controller=$matches[1]&typerocket_action=$matches[2]';
    add_rewrite_rule( $regex, $location, 'top' );
});

add_filter( 'query_vars', function($vars) {
    array_push($vars, 'typerocket_controller');
    array_push($vars, 'typerocket_action');
    return $vars;
} );

add_filter( 'template_include', function($template) {

    $typerocket_controller = get_query_var('typerocket_controller', null);
    $typerocket_action = get_query_var('typerocket_action', null);

    if($typerocket_controller && $typerocket_action) {
        $template = __DIR__ . '/api/v1.php';
    }

    return $template;
}, 99 );

define( 'TR_END', microtime( true ) );