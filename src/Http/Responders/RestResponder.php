<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Middleware\Client,
    \TypeRocket\Http\Middleware\Controller,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response,
    \TypeRocket\Http\Middleware\ValidateCsrf;

class RestResponder implements Responder
{

    private $resource = null;

    public function respond( $id )
    {
        $request  = new Request( $this->resource, $id );
        $response = new Response();
        $client = new Client($request, $response);
        $controller = new Controller($request, $response, $client);
        $middleware = new ValidateCsrf($request, $response, $controller);
        $middleware->handle();

        status_header( $response->getStatus() );
        wp_send_json( $response->getResponseArray() );
    }

    public function setResource( $resource )
    {
        $this->resource = $resource;

        return $this;
    }

}
