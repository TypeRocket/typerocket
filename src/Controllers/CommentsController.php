<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\CommentsModel;

class CommentsController extends Controller
{

    /**
     * Update comment based on ID
     *
     * @param null $id
     */
    public function update( $id = null )
    {
        $comments = new CommentsModel();
        $errors   = $comments->findById( $id )->update( $this->request->getFields() )->getErrors();

        if ( ! empty ( $errors )) {
            $this->response->setMessage( 'Comment not updated' );
            $this->response->setError( 'model', $errors );
            $this->response->setInvalid();
        } else {
            $this->response->setMessage( 'Comment updated' );
            $this->response->setData('resourceId', $comments->getId());
        }

    }

    /**
     * Create Comment
     */
    public function create()
    {
        $comments = new CommentsModel();
        $errors   = $comments->create( $this->request->getFields() )->getErrors();

        if ( ! empty ( $errors )) {
            $this->response->setMessage( 'Comment not created' );
            $this->response->setError( 'model', $errors );
            $this->response->setInvalid();
        } else {
            $this->response->setMessage( 'Comment created' );
            $this->response->setStatus(201);
            $this->response->setData('resourceId', $comments->getId());
        }

    }
}
