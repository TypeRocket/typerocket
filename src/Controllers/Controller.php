<?php
namespace TypeRocket\Controllers;

use \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

/**
 * Class Controller
 *
 * Be sure to validate by https://codex.wordpress.org/Roles_and_Capabilities
 * when building your own controllers.
 *
 * @package TypeRocket\Controllers
 */
abstract class Controller
{

    /** @var \TypeRocket\Http\Response */
    protected $response = null;
    /** @var \TypeRocket\Http\Request */
    protected $request = null;
    /** @var \WP_User $user */
    protected $user = null;

    public function __construct( Request $request, Response $response, \WP_User $user )
    {
        $this->response = $response;
        $this->request  = $request;
        $this->user     = $user;
        $this->init();
        $this->authenticate();
    }

    protected function init()
    {
        return $this;
    }

    abstract function update( $id );

    abstract function create();

    abstract protected function authenticate();

}