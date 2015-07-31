<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\CommentsModel;

class CommentsController extends Controller
{

    public function authenticate()
    {
        $comment = get_comment( $this->request->getResourceId() );

        if ($comment->user_id != $this->user->ID && ! current_user_can( 'edit_comment' )) {
            $this->response->setValid( false );
            $this->response->setError( 'auth', false );
            $this->response->setStatus(401);
            $this->response->setMessage( "Sorry, you don't have enough rights." );
        }
        
    }

    public function update( $id = null )
    {
        $comments = new CommentsModel();
        $errors   = $comments->findById( $id )->update( $this->request->getFields() )->getErrors();

        if ( ! empty ( $errors )) {
            $this->response->setMessage( 'Comment not updated' );
            $this->response->setError( 'model', $errors );
            $this->response->setValid( false );
        } else {
            $this->response->setMessage( 'Comment updated' );
        }

    }

    public function create()
    {
        $comments = new CommentsModel();
        $errors   = $comments->create( $this->request->getFields() )->getErrors();

        if ( ! empty ( $errors )) {
            $this->response->setMessage( 'Comment not created' );
            $this->response->setError( 'model', $errors );
            $this->response->setValid( false );
        } else {
            $this->response->setMessage( 'Comment created' );
            $this->response->setStatus(201);
        }

    }
}
