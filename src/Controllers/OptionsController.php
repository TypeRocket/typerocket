<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\OptionsModel;

class OptionsController extends Controller
{

    public function validate()
    {
        $valid = $this->response->getValid();

        if ( ! current_user_can( 'manage_options' )) {
            $valid                     = false;
            $this->response['message'] = "Sorry, you don't have enough rights.";
        }

        if (
            $this->request->getType() != 'RestResponder' &&
            ! check_ajax_referer( 'form_' . TR_SEED, '_tr_nonce_form', false )
        ) {
            $valid = false;
            $this->response->setMessage( 'Invalid CSRF Token' );
        }

        $valid = apply_filters( 'tr_options_controller_validate', $valid, $this );

        $this->response->setValid( $valid );
    }

    public function update($id)
    {
        $options = new OptionsModel();
        $options->create($this->request->getFields());
    }

    public function create()
    {
        $options = new OptionsModel();
        $options->create($this->request->getFields());
    }

}
