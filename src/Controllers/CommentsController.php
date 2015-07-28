<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\CommentsModel;

class CommentsController extends Controller
{

    function authenticate()
    {
        $valid = $this->response->getValid();
        $comment = get_comment($this->request->getResourceId());

        if ( $comment->user_id != $this->user->ID && ! current_user_can( 'edit_comment' ) ) {
            $valid = false;
            $this->response->setMessage("Sorry, you don't have enough rights.");
        }

        $valid = apply_filters( 'tr_comments_controller_authenticate', $valid, $this );
        $this->response->setValid($valid);
    }

    public function update($id = null)
    {
        $comments = new CommentsModel();
        $errors = $comments->findById($id)->update($this->request->getFields())->getErrors();

        if( ! empty ( $errors ) ) {
            $this->response->setMessage('Comment not updated');
            $this->response->setErrors($errors);
            $this->response->setValid(false);
        } else {
            $this->response->setMessage('Comment updated');
        }

    }

    public function create()
    {
        $comments = new CommentsModel();
        $errors = $comments->create($this->request->getFields())->getErrors();

        if( ! empty ( $errors ) ) {
            $this->response->setMessage('Comment not created');
            $this->response->setErrors($errors);
            $this->response->setValid(false);
        } else {
            $this->response->setMessage('Comment created');
        }

    }
}
