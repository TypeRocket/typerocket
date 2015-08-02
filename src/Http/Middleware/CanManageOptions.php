<?php
namespace TypeRocket\Http\Middleware;


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