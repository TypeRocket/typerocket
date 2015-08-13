<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class RestResponder extends Responder
{

    private $resource = null;

    /**
     * Respond to REST requests
     *
     * Create proper request and run through Kernel
     *
     * @param $id
     */
    public function respond( $id )
    {
        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $method = ( isset( $_POST['_method'] ) ) ? $_POST['_method'] : $method;

        $request  = new Request( $this->resource, $method, $id );
        $response = new Response();

        $this->runKernel($request, $response, 'restGlobal');

        status_header( $response->getStatus() );
        wp_send_json( $response->getResponseArray() );
    }

    /**
     * Set the resource use to construct the Request
     *
     * @param $resource
     *
     * @return $this
     */
    public function setResource( $resource )
    {
        $this->resource = $resource;

        return $this;
    }

}
