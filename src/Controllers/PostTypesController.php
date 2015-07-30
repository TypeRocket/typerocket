<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\PostTypesModel;

class PostTypesController extends Controller
{
    /** @var PostTypesModel */
    protected $model = null;
    protected $type = null;

    protected function init() {
        $reflect = new \ReflectionClass($this);
        $type = substr($reflect->getShortName(),0, -10);
        $this->type = $type;

        $class = "\\TypeRocket\\Models\\{$type}Model";
        $this->model = new $class();
    }

    protected function authenticate()
    {
        $valid = $this->response->getValid();
        $post = get_post($this->request->getResourceId());

        if ( $post->post_author != $this->user->ID && ! current_user_can( 'edit_posts') ) {
            $valid = false;
            $this->response->setMessage("Sorry, you don't have enough rights.");
        }

        $valid = apply_filters( 'tr_controller_authenticate_posts', $valid, $this );

        $this->response->setValid($valid);
    }

    /**
     * @param null $id
     *
     * @return $this
     */
    public function update($id = null)
    {
        $errors =  $this->model->findById($id)->update( $this->request->getFields() )->getErrors();

        if( ! empty ( $errors ) ) {
            $this->response->setMessage("{$this->type} not updated");
            $this->response->setErrors($errors);
            $this->response->setValid(false);
        } else {
            $this->response->setMessage( $this->type.' updated');
            $this->response->setData('post', $this->model->getData('post'));
        }

    }

    public function create()
    {
        $errors = $this->model->create($this->request->getFields() )->getErrors();

        if( ! empty ( $errors ) ) {
            $this->response->setMessage($this->type.' not created');
            $this->response->setErrors($errors);
            $this->response->setValid(false);
        } else {
            $this->response->setMessage($this->type.' created');
            $this->response->setData('post', $this->model->getData('post'));
        }

    }
}
