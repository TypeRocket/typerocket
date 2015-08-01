<?php
/**
 * Created by PhpStorm.
 * User: kevindees
 * Date: 7/31/15
 * Time: 9:15 PM
 */

namespace TypeRocket\Http;

use \TypeRocket\Http\Middleware\Client;

abstract class Kernel
{

    protected $middleware = array();

    public function __construct(Request $request, Response $response) {

        $client = new Client($request, $response);
        $middleware = array_reverse($this->middleware);

        foreach($middleware as $class) {
            $client = new $class($request, $response, $client);
        }

        $client->handle();

    }

}