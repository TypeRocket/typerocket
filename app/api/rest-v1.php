<?php
// get vars
$tr_loaded = defined( 'TR_START' );
if ($tr_loaded) {
    $tr_resource = get_query_var( 'typerocket_rest_controller', null );
    $tr_item_id  = get_query_var( 'typerocket_rest_item', null );

    // set method
    $tr_method = isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : 'GET';
    $tr_method = ( isset( $_SERVER['REQUEST_METHOD'] ) && isset( $_POST['_method'] ) ) ? $_POST['_method'] : $tr_method;

    // load
    $tr_load = current_user_can( 'read' );
    $tr_load = apply_filters( 'tr_rest_api_load', $tr_load );

    if ($tr_load) {
        $type_rocket_api = new TypeRocket\Api\RestApi($tr_resource, $tr_item_id, $tr_method, 'v1');
    }
}

status_header(404);
exit();