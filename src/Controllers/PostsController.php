<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\PostsModel;

class PostsController extends Controller
{

    protected function authenticate()
    {
        $valid = $this->response->getValid();
        $post = get_post($this->request->getResourceId());

        if ( $post->post_author != $this->user->ID && ! current_user_can( 'edit_posts') ) {
            $valid = false;
            $this->response->setMessage("Sorry, you don't have enough rights.");
        }

        $valid = apply_filters( 'tr_posts_controller_authenticate', $valid, $this );

        $this->response->setValid($valid);
    }

    /**
     * @param null $id
     *
     * @return $this
     */
    public function update($id = null)
    {
        $posts = new PostsModel();
        $errors =  $posts->findById($id)->update( $this->request->getFields() )->getErrors();

        if( ! empty ( $errors ) ) {
            $this->response->setMessage('Post not updated');
            $this->response->setErrors($errors);
            $this->response->setValid(false);
        } else {
            $this->response->setMessage('Post updated');
        }

    }

    public function create()
    {
        $posts = new PostsModel();
        $errors = $posts->create($this->request->getFields() )->getErrors();

        if( ! empty ( $errors ) ) {
            $this->response->setMessage('Post not created');
            $this->response->setErrors($errors);
            $this->response->setValid(false);
        } else {
            $this->response->setMessage('Post created');
        }

    }
}
