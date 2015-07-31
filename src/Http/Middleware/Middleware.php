<?php
namespace TypeRocket\Http\Middleware;

use \TypeRocket\Http\Response,
    \TypeRocket\Http\Request;

abstract class Middleware
{

    /** @var null|Middleware $middleware */
    protected $next = null;
    protected $request = null;
    protected $response = null;

    public function __construct( Request $request, Response $response, $middleware = null)
    {
    	$this->next = $middleware;
    	$this->request = $request;
    	$this->response = $response;
    }

    abstract public function handle();
}