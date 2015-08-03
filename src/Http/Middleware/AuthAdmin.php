<?php
namespace TypeRocket\Http\Middleware;

/**
 * Class AuthAdmin
 *
 * Authenticate user as administrator and if the user is not
 * invalidate the response.
 *
 * @package TypeRocket\Http\Middleware
 */
class AuthAdmin extends Middleware
{

    public function handle() {

        if ( ! current_user_can('administrator')) {
            $this->response->setInvalid();
            $this->response->setError( 'auth', false );
            $this->response->setStatus(401);
            $this->response->setMessage( "Sorry, you don't have enough rights." );
        }

        $this->next->handle();
    }

}