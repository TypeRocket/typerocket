<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\UsersModel;

class UsersController extends Controller
{

    public function update( $id = null )
    {
        $user   = new UsersModel();
        $errors = $user->findById( $id )->update( $this->request->getFields() )->getErrors();

        if ( ! empty ( $errors )) {
            $this->response->setMessage( 'User not updated' );
            $this->response->setError( 'model', $errors );
            $this->response->setInvalid();
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
            $this->response->setInvalid();
        } else {
            $this->response->setMessage( 'User created' );
            $this->response->setStatus(201);
        }
    }

}
