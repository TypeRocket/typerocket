<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\UsersModel;

class UsersController extends Controller
{
    function authenticate()
    {
        $user  = get_user_by( 'id', $this->request->getResourceId() );
        $valid = $this->response->getValid();

        if ( $user->ID != $this->user->ID && ! current_user_can( 'edit_users' ) ) {
            $valid = false;
            $this->response->setMessage("Sorry, you don't have enough rights.");
        }

        $valid = apply_filters( 'tr_users_controller_authenticate', $valid, $this );
        $this->response->setValid($valid);

    }

    function update($id = null)
    {
        $user = new UsersModel();
        $errors = $user->findById($id)->update( $this->request->getFields())->getErrors();

        if( ! empty ( $errors ) ) {
            $this->response->setMessage('User not updated');
            $this->response->setErrors($errors);
            $this->response->setValid(false);
        } else {
            $this->response->setMessage('User updated');
        }
    }

    function create()
    {
        $user = new UsersModel();
        $errors = $user->create($this->request->getFields())->getErrors();

        if( ! empty ( $errors ) ) {
            $this->response->setMessage('User not created');
            $this->response->setErrors($errors);
            $this->response->setValid(false);
        } else {
            $this->response->setMessage('User created');
        }
    }

}
