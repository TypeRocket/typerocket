<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Middleware\Client,
    \TypeRocket\Http\Middleware\Controller,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response,
    \TypeRocket\Http\Middleware\Csrf;

class RestResponder implements Responder
{

    private $resource = null;

    public function respond( $id )
    {
        $request  = new Request( $this->resource, $id, 'RestResponder' );
        $response = new Response();
        $client = new Client($request, $response);
        $controller = new Controller($request, $response, $client);
        new Csrf($request, $response, $controller);

        status_header( $response->getStatus() );
        wp_send_json( $response->getResponseArray() );
    }

    public function setResource( $resource )
    {
        $this->resource = $resource;

        return $this;
    }

}
