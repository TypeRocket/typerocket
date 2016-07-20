<?php
namespace TypeRocket\Http\Responders;

use TypeRocket\Http\Redirect;
use \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;
use TypeRocket\Sanitize;
use TypeRocket\View;

class ResourceResponder extends Responder
{

    private $resource = null;
    private $action = null;

    /**
     * Respond to REST requests
     *
     * Create proper request and run through Kernel
     *
     * @param $id
     */
    public function respond( $id )
    {
        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $method = ( isset( $_POST['_method'] ) ) ? $_POST['_method'] : $method;

        $request  = new Request( $this->resource, $method, $id, $this->action );
        $response = new Response();

        $this->runKernel($request, $response, 'pageGlobal');
        $returned = $this->kernel->router->returned;

        if( $returned ) {

            if( $returned instanceof Redirect ) {
                $returned->now();
            }

            if( is_string($returned) ) {
                echo $returned;
            }

            if( is_array($returned) ) {
                wp_send_json($returned);
            }

        } else {
            status_header( $response->getStatus() );
            wp_send_json( $response->getResponseArray() );
        }
    }

    /**
     * Set the resource use to construct the Request
     *
     * @param $resource
     *
     * @return $this
     */
    public function setResource( $resource )
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Set the action
     *
     * @param $action
     *
     * @return $this
     */
    public function setAction( $action ) {
        $this->action = $action;

        return $this;
    }

}
