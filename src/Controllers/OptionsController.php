<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\OptionsModel;

class OptionsController extends Controller
{

    public function authenticate()
    {
        $valid = $this->response->getValid();

        if ( ! current_user_can( 'manage_options' )) {
            $valid                     = false;
            $this->response['message'] = "Sorry, you don't have enough rights.";
        }

        $valid = apply_filters( 'tr_options_controller_authenticate', $valid, $this );

        $this->response->setValid( $valid );
    }

    public function update($id)
    {
        $options = new OptionsModel();
        $errors = $options->create( $this->request->getFields() )->getErrors();

        if( ! empty ( $errors ) ) {
            $this->response->setMessage('Options not updated');
            $this->response->setErrors($errors);
            $this->response->setValid(false);
        } else {
            $this->response->setMessage('Updated');
        }

    }

    public function create()
    {
        $options = new OptionsModel();
        $errors = $options->create($this->request->getFields())->getErrors();

        if( ! empty ( $errors ) ) {
            $this->response->setMessage('Options not created');
            $this->response->setErrors($errors);
            $this->response->setValid(false);
        } else {
            $this->response->setMessage('Options updated');
        }

    }

}
