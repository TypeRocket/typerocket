<?php
namespace TypeRocket\Http\Middleware;

use TypeRocket\Controllers\Controller;

/**
 * Class Router
 *
 * Run proper controller based on request.
 *
 * @package TypeRocket\Http\Middleware
 */
class Router extends Middleware
{
    public $returned = [];
    /** @var Controller  */
    protected $controller;
    public $middleware = [];
    public $action;
    public $item_id;

    public function init()
    {
        $this->action = $this->getAction();
        $resource = ucfirst( $this->request->getResource() );
        $controller  = "\\TypeRocket\\Controllers\\{$resource}Controller";

        if( ! class_exists( $controller ) ) {
            $controller  = "\\" . TR_APP_NAMESPACE . "\\Controllers\\{$resource}Controller";
        }

        if ( class_exists( $controller ) ) {
            $this->controller = $controller = new $controller( $this->request, $this->response);

            if ( ! $controller instanceof Controller || ! method_exists( $controller, $this->action ) ) {
                $this->response->setError( 'controller', 'Routing error');
                $this->response->setStatus(405);
                $this->response->setInvalid();
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

                if (array_key_exists('except', $group) && ! in_array($this->action, $group['except'])) {
                    $use = $group['group'];
                }

                if (array_key_exists('only', $group) && in_array($this->action, $group['only'])) {
                    $use = $group['group'];
                }

                $groups[] = $use;
            }
        }

        return $groups;
    }

    protected function getAction() {
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
            case 'edit' :
                if( $method == 'PUT' ) {
                    $action = 'update';
                } else {
                    $action = 'edit';
                }
                break;
            case 'delete' :
                if( $method == 'DELETE' ) {
                    $action = 'delete';
                } else {
                    wp_die('This page is not for viewing');
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
                $action = $request->getAction();
                break;
        }

        return $action;
    }

}