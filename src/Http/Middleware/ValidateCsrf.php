<?php
namespace TypeRocket\Http\Middleware;

use \TypeRocket\Config;

class ValidateCsrf extends Middleware  {

    public function handle() {

        $token = check_ajax_referer( 'form_' . Config::getSeed(), '_tr_nonce_form', false );
        if ( ! $token) {
            $this->response->setValid( false );
            $this->response->setError( 'csrf', true );
            $this->response->setMessage( 'Invalid CSRF Token' );
        }

        $this->next->handle();
    }
}
