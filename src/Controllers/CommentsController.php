<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\CommentsModel;

class CommentsController extends Controller
{

    /**
     * Update comment based on ID
     *
     * @param null $id
     *
     * @return mixed|void
     */
    public function update( $id = null )
    {
        $comments = new CommentsModel();
        $errors   = $comments->findById( $id )->update( $this->request->getFields() )->getErrors();

        if ( ! empty ( $errors )) {
            $this->response->flashNext( 'Comment not updated', 'error' );
            $this->response->setError( 'model', $errors );
        } else {
            $this->response->flashNext( 'Comment updated', 'success' );
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
            $this->response->flashNext( 'Comment not created', 'error' );
            $this->response->setError( 'model', $errors );
        } else {
            $this->response->flashNext( 'Comment created', 'success' );
            $this->response->setStatus(201);
            $this->response->setData('resourceId', $comments->getId());
        }

    }
}
