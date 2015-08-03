<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\UsersModel;

class UsersController extends Controller
{

    /**
     * Update user by ID
     *
     * @param null $id
     */
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
            $this->response->setData('resourceId', $user->getId());
        }
    }

    /**
     * Create user
     */
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
            $this->response->setData('resourceId', $user->getId());
        }
    }

}
