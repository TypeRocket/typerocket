<?php
$tr_loaded = defined( 'TR_START' );
if ($tr_loaded) {
    $tr_resource = get_query_var( 'typerocket_rest_controller', null );
    $tr_item_id  = get_query_var( 'typerocket_rest_item', null );

    $tr_load = apply_filters( 'tr_rest_api_load', true, $tr_resource, $tr_item_id );
    if ($tr_load) {
        $restResponder = new TypeRocket\Http\Responders\RestResponder();
        $restResponder->setResource($tr_resource);
        $restResponder->respond($tr_item_id);
    }
}

status_header(404);
die();