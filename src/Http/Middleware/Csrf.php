<?php
namespace TypeRocket\Http\Middleware;

use \TypeRocket\Http\Response,
    \TypeRocket\Http\Request,
    \TypeRocket\Config;

class Csrf extends Middleware  {

    public function handle(Request $request, Response $response) {

        $token = check_ajax_referer( 'form_' . Config::getSeed(), '_tr_nonce_form', false );
        if ( ! $token) {
            $response->setValid( false );
            $response->setError( 'csrf', true );
            $response->setMessage( 'Invalid CSRF Token' );
        }

        $this->next->handle($request, $response);
    }
}
