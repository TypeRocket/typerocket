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
            $this->response->setMessage( 'Options not updated' );
            $this->response->setError( 'model', $errors );
            $this->response->setInvalid();
        } else {
            $this->response->setMessage( 'Updated' );
            $this->response->setData('resourceId', $options->getId());
        }

    }

    /**
     * Create option
     */
    public function create()
    {
        $options = new OptionsModel();
        $errors  = $options->create( $this->request->getFields() )->getErrors();

        if ( ! empty ( $errors )) {
            $this->response->setMessage( 'Options not created' );
            $this->response->setError( 'model', $errors );
            $this->response->setInvalid();
            $this->response->setMessage( 'Options updated' );
            $this->response->setData('resourceId', $options->getId());
        }

    }

}
