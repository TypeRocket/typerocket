<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\UsersModel;

class UsersController extends Controller
{

    public function authenticate()
    {
        $user  = get_user_by( 'id', $this->request->getResourceId() );

        if ($user->ID != $this->user->ID && ! current_user_can( 'edit_users' )) {
            $this->response->setValid( false );
            $this->response->setError( 'auth', false );
            $this->response->setStatus(401);
            $this->response->setMessage( "Sorry, you don't have enough rights." );
        }

    }

    public function update( $id = null )
    {
        $user   = new UsersModel();
        $errors = $user->findById( $id )->update( $this->request->getFields() )->getErrors();

        if ( ! empty ( $errors )) {
            $this->response->setMessage( 'User not updated' );
            $this->response->setError( 'model', $errors );
            $this->response->setValid( false );
        } else {
            $this->response->setMessage( 'User updated' );
        }
    }

    public function create()
    {
        $user   = new UsersModel();
        $errors = $user->create( $this->request->getFields() )->getErrors();

        if ( ! empty ( $errors )) {
            $this->response->setMessage( 'User not created' );
            $this->response->setError( 'model', $errors );
            $this->response->setValid( false );
        } else {
            $this->response->setMessage( 'User created' );
            $this->response->setStatus(201);
        }
    }

}
