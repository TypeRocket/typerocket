<?php
namespace TypeRocket\Http;

use TypeRocket\Http\Middleware\Controller;

class Kernel
{

    protected $middleware = array(
        '\\TypeRocket\\Http\\Middleware\\AuthRead'
    );

    public function __construct(Request $request, Response $response) {

        $client = new Controller($request, $response);
        $middleware = array_reverse($this->middleware);


        foreach($middleware as $class) {
            $client = new $class($request, $response, $client);
        }

        $client->handle();

    }

}