<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Config,
    \TypeRocket\Http\Kernel,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class RestResponder
{

    private $resource = null;

    public function respond( $id )
    {
        $request  = new Request( $this->resource, $id, 'RestResponder' );
        $response = new Response();
        $method   = $request->getMethod();
        $action   = null;

        switch ($method) {
            case 'PUT' :
                $action = 'update';
                break;
            case 'GET' :
                $action = 'read';
                break;
            case 'DELETE' :
                $action = 'delete';
                break;
            case 'POST' :
                $action = 'create';
                break;
        }

        $token = check_ajax_referer( 'form_' . Config::getSeed(), '_tr_nonce_form', false );
        if ( ! $token) {
            $response->setValid( false );
            $response->setError( 'csrf', true );
            $response->setMessage( 'Invalid CSRF Token' );
        }

        new Kernel( $action, $request, $response );

        status_header( $response->getStatus() );
        wp_send_json( $response->getResponseArray() );

    }

    public function setResource( $resource )
    {
        $this->resource = $resource;

        return $this;
    }

}
