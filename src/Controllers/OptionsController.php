<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\OptionsModel;

class OptionsController extends Controller
{

    /**
     * Update option
     *
     * @param $id
     */
    public function update( $id )
    {
        $options = new OptionsModel();
        $errors  = $options->create( $this->request->getFields() )->getErrors();

        if ( ! empty ( $errors )) {
            $this->response->flashNotice( 'Options not updated', 'error' );
            $this->response->setError( 'model', $errors );
        } else {
            $this->response->flashNotice( 'Updated', 'success' );
        }

    }

    /**
     * Create option
     */
    public function create()
    {
        $options = new OptionsModel();
        $errors  = $options->create( $this->request->getFields() )->getErrors();

        if ( ! empty( $errors ) ) {
            $this->response->flashNotice( 'Options not created', 'error' );
            $this->response->setError( 'model', $errors );
        } else {
            $this->response->flashNotice( 'Options created', 'success' );
        }

    }

}
