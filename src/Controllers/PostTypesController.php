<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\PostTypesModel;

class PostTypesController extends Controller
{

    /** @var PostTypesModel */
    protected $model = null;
    protected $type = null;

    protected function init()
    {
        $reflect    = new \ReflectionClass( $this );
        $type       = substr( $reflect->getShortName(), 0, - 10 );
        $this->type = $type;

        $class       = "\\TypeRocket\\Models\\{$type}Model";
        $this->model = new $class();
    }

    protected function authenticate()
    {
        $post  = get_post( $this->request->getResourceId() );

        if ($post->post_author != $this->user->ID && ! current_user_can( 'edit_posts' )) {
            $this->response->setValid( false );
            $this->response->setError( 'auth', false );
            $this->response->setStatus(401);
            $this->response->setMessage( "Sorry, you don't have enough rights." );
        }
    }

    /**
     * @param null $id
     *
     * @return $this
     */
    public function update( $id = null )
    {
        $errors = $this->model->findById( $id )->update( $this->request->getFields() )->getErrors();

        if ( ! empty ( $errors )) {
            $this->response->setMessage( "{$this->type} not updated" );
            $this->response->setError( 'model', $errors );
            $this->response->setValid( false );
        } else {
            $this->response->setMessage( $this->type . ' updated' );
        }

    }

    public function create()
    {
        $errors = $this->model->create( $this->request->getFields() )->getErrors();

        if ( ! empty ( $errors )) {
            $this->response->setMessage( $this->type . ' not created' );
            $this->response->setError( 'model', $errors );
            $this->response->setValid( false );
        } else {
            $this->response->setMessage( $this->type . ' created' );
            $this->response->setStatus(201);
        }

    }
}
