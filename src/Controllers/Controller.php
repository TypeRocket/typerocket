<?php
namespace TypeRocket\Controllers;

use \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

/**
 * Class Controller
 *
 * Be sure to validate by https://codex.wordpress.org/Roles_and_Capabilities
 * when building your own controllers. You can do this with Middleware and
 * XKernel.
 *
 * @package TypeRocket\Controllers
 */
abstract class Controller
{

    /** @var \TypeRocket\Http\Response */
    protected $response = null;
    /** @var \TypeRocket\Http\Request */
    protected $request = null;

    public function __construct( Request $request, Response $response )
    {
        $this->response = $response;
        $this->request  = $request;
        $this->init();
    }

    protected function init()
    {
        return $this;
    }

    abstract function update( $id );

    abstract function create();

}