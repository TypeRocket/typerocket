<?php
namespace TypeRocket\Http\Middleware;

use \TypeRocket\Http\Response,
    \TypeRocket\Http\Request;

class Client
{
    public function handle( Request $request, Response $response )
    {
        if($request->getType() == 'RestResponder') {
            status_header( $response->getStatus() );
            wp_send_json( $response->getResponseArray() );
        }
    }
}