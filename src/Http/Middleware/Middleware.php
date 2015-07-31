<?php
namespace TypeRocket\Http\Middleware;

use \TypeRocket\Http\Response,
    \TypeRocket\Http\Request;

abstract class Middleware
{

    /** @var null|Middleware $middleware */
    public $next = null;

    public function __construct( Request $request, Response $response, $middleware = null)
    {
        $this->next = $middleware;
        $this->handle( $request, $response );
    }

    abstract public function handle( Request $request, Response $response );
}