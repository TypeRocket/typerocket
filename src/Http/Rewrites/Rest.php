<?php

namespace TypeRocket\Http\Rewrites;

use TypeRocket\Http\Request;
use TypeRocket\Http\Responders\ResourceResponder;

class Rest
{

    public function __construct()
    {
        if ( defined( 'TR_START' ) ) {
            $tr_resource = get_query_var( 'typerocket_rest_controller', null );
            $tr_item_id  = get_query_var( 'typerocket_rest_item', null );

            $tr_load = apply_filters( 'tr_rest_api_load', true, $tr_resource, $tr_item_id );
            if ($tr_load) {

                $request = new Request();
                $method = $request->getFormMethod();
                if( $method == 'PUT' ) {
                    $action = 'update';
                } elseif( $method == 'DELETE' ) {
                    $action = 'destroy';
                } else {
                    $action = 'create';
                }

                $restResponder = new ResourceResponder();
                $restResponder->setAction($action);
                $restResponder->setResource($tr_resource);
                $restResponder->respond($tr_item_id);
            }
        }

        status_header(404);
        die();
    }

}