<?php
add_action('admin_init', function() {

    // Controller API
    $regex = 'typerocket_rest_api/v1/([^/]*)/?$';
    $location = 'index.php?typerocket_rest_controller=$matches[1]';
    add_rewrite_rule( $regex, $location, 'top' );

    // Rest API
    $regex = 'typerocket_rest_api/v1/([^/]*)/([^/]*)/?$';
    $location = 'index.php?typerocket_rest_controller=$matches[1]&typerocket_rest_item=$matches[2]';
    add_rewrite_rule( $regex, $location, 'top' );

    // Matrix API
    $regex = 'typerocket_matrix_api/v1/([^/]*)/([^/]*)/([^/]*)/?$';
    $location = 'index.php?typerocket_matrix_group=$matches[1]&typerocket_matrix_type=$matches[2]&typerocket_matrix_folder=$matches[3]';
    add_rewrite_rule( $regex, $location, 'top' );

    // Builder API
    $regex = 'typerocket_builder_api/v1/([^/]*)/([^/]*)/([^/]*)/?$';
    $location = 'index.php?typerocket_builder_group=$matches[1]&typerocket_builder_type=$matches[2]&typerocket_builder_folder=$matches[3]';
    add_rewrite_rule( $regex, $location, 'top' );

});

add_filter( 'query_vars', function($vars) {
    $vars[] = 'typerocket_rest_controller';
    $vars[] = 'typerocket_rest_item';
    $vars[] = 'typerocket_matrix_group';
    $vars[] = 'typerocket_matrix_folder';
    $vars[] = 'typerocket_matrix_type';
    $vars[] = 'typerocket_builder_group';
    $vars[] = 'typerocket_builder_folder';
    $vars[] = 'typerocket_builder_type';
    return $vars;
} );

add_filter( 'template_include', function($template) {

    $resource = get_query_var('typerocket_rest_controller', null);

    $load_template = ($resource);
    $load_template = apply_filters('tr_rest_api_template', $load_template);

    if($load_template) {
        require __DIR__ . '/rest-v1.php';
        die();
    }

    return $template;
}, 99 );

add_filter( 'template_include', function($template) {

    $matrix_group = get_query_var('typerocket_matrix_group', null);
    $matrix_type = get_query_var('typerocket_matrix_type', null);
    $matrix_folder = get_query_var('typerocket_matrix_folder', null);

    $load_template = ($matrix_group && $matrix_type && $matrix_folder);
    $load_template = apply_filters('tr_matrix_api_template', $load_template);

    if($load_template) {
        require __DIR__ . '/matrix-v1.php';
        die();
    }

    return $template;
}, 99 );

add_filter( 'template_include', function($template) {

    $builder_group = get_query_var('typerocket_builder_group', null);
    $builder_type = get_query_var('typerocket_builder_type', null);
    $builder_folder = get_query_var('typerocket_builder_folder', null);

    $load_template = ($builder_group && $builder_type && $builder_folder );
    $load_template = apply_filters('tr_builder_api_template', $load_template);

    if($load_template) {
        require __DIR__ . '/builder-v1.php';
        die();
    }

    return $template;
}, 99 );

add_action( 'rest_api_init', function () {
    register_rest_route( 'typerocket/v1', '/search', [
        'methods' => 'GET',
        'callback' => '\\TypeRocket\\WpRestApi::search'
    ]);
} );