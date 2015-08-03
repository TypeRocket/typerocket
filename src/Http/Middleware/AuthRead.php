<?php
namespace TypeRocket\Http\Middleware;

/**
 * Class AuthRead
 *
 * Authenticate user has read access and if the user does not
 * invalidate the response.
 *
 * @package TypeRocket\Http\Middleware
 */
class AuthRead extends Middleware  {

    public function handle() {

        if ( ! current_user_can('read')) {
            $this->response->setInvalid();
            $this->response->setError( 'auth', false );
            $this->response->setStatus(401);
            $this->response->setMessage( "Sorry, you don't have enough rights." );
        }

        $this->next->handle();
    }
}
