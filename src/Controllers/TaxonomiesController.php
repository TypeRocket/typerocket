<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\TaxonomiesModel;

class TaxonomiesController extends Controller
{

    /** @var TaxonomiesModel */
    protected $model = null;
    protected $type = null;

    /**
     * Dynamically load proper Model based on post type
     */
    protected function init()
    {
        $reflect    = new \ReflectionClass( $this );
        $type       = substr( $reflect->getShortName(), 0, - 10 );
        $this->type = $type;

        $class       = "\\TypeRocket\\Models\\{$type}Model";
        $this->model = new $class();
    }

    /**
     * Update Taxonomy Term by ID
     *
     * @param null $id
     */
    public function update( $id = null )
    {
        $errors = $this->model->findById( $id )->update( $this->request->getFields() )->getErrors();

        if ( ! empty ( $errors )) {
            $this->response->setMessage( "{$this->type} not updated" );
            $this->response->setError( 'model', $errors );
            $this->response->setInvalid();
        } else {
            $this->response->setMessage( $this->type . ' updated' );
            $this->response->setData('resourceId', $this->model->getId());
        }

    }

    /**
     * Create Taxonomy Term
     */
    public function create()
    {
        $errors = $this->model->create( $this->request->getFields() )->getErrors();

        if ( ! empty ( $errors )) {
            $this->response->setMessage( $this->type . ' not created' );
            $this->response->setError( 'model', $errors );
            $this->response->setInvalid();
        } else {
            $this->response->setMessage( $this->type . ' created' );
            $this->response->setStatus(201);
            $this->response->setData('resourceId', $this->model->getId());
        }

    }
}
