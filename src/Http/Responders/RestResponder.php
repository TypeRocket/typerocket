<?php
namespace TypeRocket\Http\Responders;

use TypeRocket\Http\RestKernel,
    \TypeRocket\Http\Controller,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class RestResponder implements Responder
{

    private $resource = null;

    public function respond( $id )
    {
        $request  = new Request( $this->resource, $id );
        $response = new Response();

        new RestKernel($request, $response);
        new Controller($request, $response);

        status_header( $response->getStatus() );
        wp_send_json( $response->getResponseArray() );
    }

    public function setResource( $resource )
    {
        $this->resource = $resource;

        return $this;
    }

}
