<?php
namespace TypeRocket\Http;

use TypeRocket\Controllers\Controller;

/**
 * Class Router
 *
 * Run proper controller based on request.
 *
 * @package TypeRocket\Http\Middleware
 */
class Router
{
    public $returned = [];
    protected $request = null;
    protected $response = null;
    /** @var Controller  */
    protected $controller;
    public $middleware = [];
    public $action;
    public $item_id;

    public function __construct( Request $request, Response $response, $action_method = 'GET' )
    {
        $this->request = $request;
        $this->response = $response;
        $this->action = $this->getAction( $action_method );
        $resource = ucfirst( $this->request->getResource() );
        $controller  = "\\TypeRocket\\Controllers\\{$resource}Controller";

        if( ! class_exists( $controller ) ) {
            $controller  = "\\" . TR_APP_NAMESPACE . "\\Controllers\\{$resource}Controller";
        }

        if ( class_exists( $controller ) ) {
            $this->controller = $controller = new $controller( $this->request, $this->response);

            try {
                $param = new \ReflectionParameter(array($controller, $this->action), 0);
            } catch( \Exception $e) {
                $param = null;
            }

            if( $this->request->getResourceId() && ! $param ) {
                $this->response->setMessage('Something went wrong with item');
                $this->response->exitAny(405);
            }

            if ( ! $controller instanceof Controller || ! method_exists( $controller, $this->action ) ) {
                $this->response->setMessage('Something went wrong');
                $this->response->exitAny(405);
            } else {
                $this->item_id    = $this->request->getResourceId();
                $this->middleware = $this->controller->getMiddleware();
            }
        } else {
            wp_die('Missing controller: ' . $controller );
        }
    }

    public function handle() {
        $action = $this->action;
        $controller = $this->controller;
        $this->returned = $controller->$action( $this->item_id );
    }

    public function getMiddlewareGroups() {
        $groups = [];

        foreach ($this->middleware as $group ) {
            if (array_key_exists('group', $group)) {
                $use = null;

                if( ! array_key_exists('except', $group) && ! array_key_exists('only', $group) ) {
                    $use = $group['group'];
                }

                if (array_key_exists('except', $group) && ! in_array($this->action, $group['except'])) {
                    $use = $group['group'];
                }

                if (array_key_exists('only', $group) && in_array($this->action, $group['only'])) {
                    $use = $group['group'];
                }

                if($use) {
                    $groups[] = $use;
                }
            }
        }

        return $groups;
    }

    protected function getAction( $action_method = 'GET' ) {
        $request = $this->request;

        $method = $request->getMethod();
        $action = null;
        switch ( $request->getAction() ) {
            case 'add' :
                if( $method == 'POST' ) {
                    $action = 'create';
                } else {
                    $action = 'add';
                }
                break;
            case 'create' :
                if( $method == 'POST' ) {
                    $action = 'create';
                }
                break;
            case 'edit' :
                if( $method == 'PUT' ) {
                    $action = 'update';
                } else {
                    $action = 'edit';
                }
                break;
            case 'update' :
                if( $method == 'PUT' ) {
                    $action = 'update';
                }
                break;
            case 'delete' :
                if( $method == 'DELETE' ) {
                    $action = 'destroy';
                } else {
                    $action = 'delete';
                }
                break;
            case 'index' :
                if( $method == 'GET' ) {
                    $action = 'index';
                }
                break;
            case 'show' :
                if( $method == 'GET' ) {
                    $action = 'show';
                }
                break;
            default :
                if($action_method == $method ) {
                    $action = $request->getAction();
                }
                break;
        }

        return $action;
    }

}