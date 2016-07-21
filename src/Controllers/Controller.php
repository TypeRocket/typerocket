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

    protected $middleware = [];
    protected $modelClass = null;

    public function __construct( Request $request, Response $response )
    {
        $this->response = $response;
        $this->request  = $request;
        $this->init();
        $this->routing();
    }

    /**
     * @return $this
     */
    protected function init()
    {
        return $this;
    }

    /**
     * @return $this
     */
    protected function routing()
    {
        return $this;
    }

    /**
     * @return array
     */
    public function getMiddleware() {
        return $this->middleware;
    }

    /**
     * @param $group
     *
     * @param array $settings
     *
     * @return $this
     */
    public function setMiddleware( $group, $settings = []) {
        $middleware['group'] = $group;

        if(array_key_exists('except', $settings)) {
            $middleware['except'] = $settings['except'];
        }

        if(array_key_exists('only', $settings)) {
            $middleware['only'] = $settings['only'];
        }

        $this->middleware[] = $middleware;

        return $this;
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param $id
     *
     * @return mixed
     */
    abstract function update( $id );

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    abstract function create();

}