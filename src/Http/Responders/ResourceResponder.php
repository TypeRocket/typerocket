<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Redirect,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class ResourceResponder extends Responder
{

    private $resource = null;
    private $action = null;
    private $actionMethod = null;

    /**
     * Respond to REST requests
     *
     * Create proper request and run through Kernel
     *
     * @param $id
     */
    public function respond( $id )
    {
        $request  = new Request( $this->resource, null, $id, $this->action );
        $response = new Response();

        $this->runKernel($request, $response, 'resourceGlobal', $this->actionMethod);
        $returned = $this->kernel->router->returned;

        if( $returned && empty($_POST['_tr_ajax_request']) ) {

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

    /**
     * Set the action method
     *
     * @param $action_method
     *
     * @return $this
     */
    public function setActionMethod( $action_method ) {
        $this->actionMethod = $action_method;

        return $this;
    }

}
