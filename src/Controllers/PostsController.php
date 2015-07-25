<?php
namespace TypeRocket\Controllers;

class PostsController extends Controller
{

    /** @var \WP_Post */
    public $post = null;

    function hook( $post_id, $post )
    {
        $this->post  = $post;
        $this->valid = true;
        $this->save( $post_id );
    }

    function getValidate()
    {
        $this->valid = parent::getValidate();

        if ( $this->post->post_author != $this->currentUser->ID && ! current_user_can( 'edit_posts') ) {
            $this->valid = false;
            $this->response['message'] = "Sorry, you don't have enough rights.";
        }

        $this->valid = apply_filters( 'tr_posts_controller_validate', $this->valid, $this );

        return $this->valid;
    }

    function filter()
    {
        parent::filter();
        $this->fields = apply_filters( 'tr_posts_controller_filter', $this->fields, $this );

        return $this;
    }

    /**
     * @param $item_id
     * @param string $action
     *
     * @return PostsController $this
     */
    function save( $item_id, $action = 'update' )
    {

        if($this->post === null && ! empty($item_id) ) {
            $this->post = get_post($item_id);
        }

        $fillable = apply_filters( 'tr_posts_controller_fillable', $this->getFillable(), $this );
        $this->setFillable($fillable);
        parent::save( $item_id, $action );

        return $this;
    }

    protected function update()
    {
        if (is_array( $this->fieldsBuiltin )) {
            remove_action( 'save_post', array( $this, 'hook' ), 1999909 );
            $this->fieldsBuiltin['ID'] = $this->item_id;
            wp_update_post( $this->fieldsBuiltin );
            add_action( 'save_post', array( $this, 'hook' ), 1999909, 3 );
        }

        $this->savePostMeta();

        do_action('tr_posts_controller_update', $this);

        return $this;
    }

    protected function create()
    {
        remove_action( 'save_post', array( $this, 'hook' ) );
        $insert        = array_merge(
            $this->defaultValues,
            $this->fieldsBuiltin,
            $this->staticValues
        );
        $post = wp_insert_post( $insert );
        add_action( 'save_post', array( $this, 'hook' ) );

        if($post instanceof \WP_Error || $post === 0 ) {
            $this->response['message'] = 'Post not created';
            $default = 'post_name (slug), post_title, post_content, and post_excerpt are required';
            $this->response['errors'] = ! empty($post->errors) ? $post->errors : array($default);
            $this->valid = false;
        } else {
            $this->item_id = $post;
        }

        $this->savePostMeta();

        do_action('tr_posts_controller_create', $this);

        return $this;
    }

    function savePostMeta()
    {

        if (is_array( $this->fields ) && ! empty($this->item_id) ) :
            if ($parent_id = wp_is_post_revision( $this->item_id )) {
                $this->item_id = $parent_id;
            }

            foreach ($this->fields as $key => $value) :
                if (is_string( $value )) {
                    $value = trim( $value );
                }

                $current_value = get_post_meta( $this->item_id, $key, true );

                if (( isset( $value ) && $value !== "" ) && $value !== $current_value) :
                    update_post_meta( $this->item_id, $key, $value );
                elseif ( ! isset( $value ) || $value === "" && ( isset( $current_value ) || $current_value === "" )) :
                    delete_post_meta( $this->item_id, $key );
                endif;

            endforeach;
        endif;
    }
}
