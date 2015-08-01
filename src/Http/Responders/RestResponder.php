<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Middleware\Client,
    \TypeRocket\Http\Middleware\ValidateCsrf,
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

        $client = new Client($request, $response);
        $middleware = new ValidateCsrf($request, $response, $client);
        $middleware->handle();
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
