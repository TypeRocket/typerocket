<?php
namespace TypeRocket\Http\Middleware;

/**
 * Class CanManageOptions
 *
 * Validate that the user can manage options and if the user can
 * not invalidate the response.
 *
 * @package TypeRocket\Http\Middleware
 */
class CanManageOptions extends Middleware
{

    public function handle() {

        if ( ! current_user_can( 'manage_options' )) {
            $this->response->setInvalid();
            $this->response->setError( 'auth', false );
            $this->response->setStatus(401);
            $this->response->setMessage( "Sorry, you don't have enough rights." );
        }

        $this->next->handle();
    }

}