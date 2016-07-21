<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\OptionsModel;

class OptionsController extends Controller
{

    /**
     * Update option
     *
     * @param $id
     *
     * @return mixed|void
     */
    public function update( $id )
    {
        $options = new OptionsModel();
        $errors  = $options->create( $this->request->getFields() )->getErrors();

        if ( ! empty ( $errors )) {
            $this->response->flashNext( 'Options not updated', 'error' );
            $this->response->setError( 'model', $errors );
        } else {
            $this->response->flashNext( 'Updated', 'success' );
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
            $this->response->flashNext( 'Options not created', 'error' );
            $this->response->setError( 'model', $errors );
        } else {
            $this->response->flashNext( 'Options created', 'success' );
        }

    }

}
