<?php
namespace TypeRocket\Http;

use TypeRocket\Http\Middleware\Controller;

class Kernel
{

    protected $middleware = array(
        'hookGlobal' =>
            array('\\TypeRocket\\Http\\Middleware\\AuthRead'),
        'restGlobal' =>
            array(
                '\\TypeRocket\\Http\\Middleware\\AuthRead',
                '\\TypeRocket\\Http\\Middleware\\ValidateCsrf'
            )
    );

    public function __construct(Request $request, Response $response, $type = 'hookGlobal') {

        $resource = strtolower( $request->getResource() );
        $resourceMiddleware = array();

        if(array_key_exists($resource, $this->middleware)) {
            $resourceMiddleware = $this->middleware[$resource];
        }

        $client = new Controller($request, $response);

        $middleware = array_merge($resourceMiddleware, $this->middleware[$type]);
        $middleware = array_reverse($middleware);

        foreach($middleware as $class) {
            $client = new $class($request, $response, $client);
        }

        $client->handle();

    }

}