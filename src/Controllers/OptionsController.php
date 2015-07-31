<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\OptionsModel;

class OptionsController extends Controller
{

    public function authenticate()
    {
        $valid = $this->response->getValid();

        if ( ! current_user_can( 'manage_options' )) {
            $$this->response->setValid( false );
            $this->response->setError( 'auth', false );
            $this->response->setStatus(401);
            $this->response->setMessage( "Sorry, you don't have enough rights." );
        }

        
    }

    public function update( $id )
    {
        $options = new OptionsModel();
        $errors  = $options->create( $this->request->getFields() )->getErrors();

        if ( ! empty ( $errors )) {
            $this->response->setMessage( 'Options not updated' );
            $this->response->setError( 'model', $errors );
            $this->response->setValid( false );
        } else {
            $this->response->setMessage( 'Updated' );
        }

    }

    public function create()
    {
        $options = new OptionsModel();
        $errors  = $options->create( $this->request->getFields() )->getErrors();

        if ( ! empty ( $errors )) {
            $this->response->setMessage( 'Options not created' );
            $this->response->setError( 'model', $errors );
            $this->response->setValid( false );
        } else {
            $this->response->setMessage( 'Options updated' );
        }

    }

}
