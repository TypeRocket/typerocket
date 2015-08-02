<?php
namespace TypeRocket\Http\Middleware;


class IsUserOrCanEditUsers extends Middleware
{

    public function handle() {

        $currentUser = wp_get_current_user();
        $user  = get_user_by( 'id', $this->request->getResourceId() );

        if ($user->ID != $currentUser->ID && ! current_user_can( 'edit_users' )) {
            $this->response->setInvalid();
            $this->response->setError( 'auth', false );
            $this->response->setStatus(401);
            $this->response->setMessage( "Sorry, you don't have enough rights." );
        }

        $this->next->handle();
    }

}