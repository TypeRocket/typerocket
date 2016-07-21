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

        if ( ! current_user_can('administrator') ) {
            $this->response->setError('auth', false);
            $this->response->flashNow( "Sorry, you don't have enough rights.", 'error' );
            $this->response->exitAny(401);
        }

        $this->next->handle();
    }

}