<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class RestResponder extends Responder
{

    private $resource = null;

    public function respond( $id )
    {
        // set method
        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $method = ( isset( $_SERVER['REQUEST_METHOD'] ) && isset( $_POST['_method'] ) ) ? $_POST['_method'] : $method;

        $request  = new Request( $this->resource, $method, $id );
        $response = new Response();

        $this->runKernel($request, $response, 'restGlobal');

        status_header( $response->getStatus() );
        wp_send_json( $response->getResponseArray() );
    }

    public function setResource( $resource )
    {
        $this->resource = $resource;

        return $this;
    }

}
