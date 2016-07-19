<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;
use TypeRocket\Sanitize;

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

        if( $response->setInvalid() ) {
            $cookie_id = Sanitize::underscore(uniqid());
            setcookie('old_fields_key', $cookie_id, time() + 1 * MINUTE_IN_SECONDS);
            set_transient( 'tr_old_fields_' . $cookie_id, $request->getFields(), 1 * MINUTE_IN_SECONDS );
        }

        if( $response->getRedirect() ) {
            wp_redirect($response->getRedirect());
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
