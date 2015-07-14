<?php
/*
|--------------------------------------------------------------------------
| Enhance WordPress
|--------------------------------------------------------------------------
|
| Enhance WordPress with a few functions that help clean up the interface
|
*/
$tr_enhance = new \TypeRocket\Enhance();
$tr_enhance->run();
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
    $tr_plugins_collection = new \TypeRocket\Plugin\PluginCollection();
    $tr_plugins_in_config = \TypeRocket\Config::getPlugins();

    foreach($tr_plugins_in_config as $plugin) {
        $tr_plugins_collection->append($plugin);
    }

	$tr_plugin_loader      = new \TypeRocket\Plugin\Loader( $tr_plugins_collection );
	$tr_plugin_loader->load();
	unset( $tr_plugin_loader );
    unset( $tr_plugins_collection );
    unset( $tr_plugins_in_config );
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
$tr_model_post = new TypeRocket\Controllers\PostsController();
add_action( 'save_post', array( $tr_model_post, 'hook' ), 1999909, 3 );
unset($tr_model_post);

$tr_model_comment = new TypeRocket\Controllers\CommentsController();
add_action( 'wp_insert_comment', array( $tr_model_comment, 'hook' ), 1999909, 3 );
add_action( 'edit_comment', array( $tr_model_comment, 'hook' ), 1999909, 3 );
unset($tr_model_comment);

$tr_model_user = new TypeRocket\Controllers\UsersController();
add_action( 'edit_user_profile_update', array( $tr_model_user, 'hook' ), 1999909, 3  );
add_action( 'personal_options_update', array( $tr_model_user, 'hook' ), 1999909, 3 );
unset($tr_model_user);

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

    $regex = 'typerocket_matrix_api/v1/([^/]*)/([^/]*)/([^/]*)/?$';
    $location = 'index.php?typerocket_matrix_group=$matches[1]&typerocket_matrix_type=$matches[2]&typerocket_matrix_form=$matches[3]';
    add_rewrite_rule( $regex, $location, 'top' );
});

add_filter( 'query_vars', function($vars) {
    array_push($vars, 'typerocket_rest_controller');
    array_push($vars, 'typerocket_rest_item');
    array_push($vars, 'typerocket_matrix_group');
    array_push($vars, 'typerocket_matrix_type');
    array_push($vars, 'typerocket_matrix_form');
    return $vars;
} );

add_filter( 'template_include', function($template) {

    $resource = get_query_var('typerocket_rest_controller', null);

    $load_template = ($resource);
    $load_template = apply_filters('tr_rest_api_template', $load_template);

    if($load_template) {
        $template = __DIR__ . '/api/rest-v1.php';
    }

    return $template;
}, 99 );

add_filter( 'template_include', function($template) {

    $matrix_group = get_query_var('typerocket_matrix_group', null);
    $matrix_type = get_query_var('typerocket_matrix_type', null);

    $load_template = ($matrix_group && $matrix_type);
    $load_template = apply_filters('tr_matrix_api_template', $load_template);

    if($load_template) {
        $template = __DIR__ . '/api/matrix-v1.php';
    }

    return $template;
}, 99 );

define( 'TR_END', microtime( true ) );