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
        $fillable = apply_filters( 'tr_posts_controller_fillable', $this->getFillable(), $this );
        $this->setFillable($fillable);
        parent::save( $item_id, $action );

        return $this;
    }

    protected function update()
    {
        if (isset( $_POST['_tr_builtin_data'] )) {
            remove_action( 'save_post', array( $this, 'hook' ), 1999909 );
            $_POST['_tr_builtin_data']['ID'] = $this->item_id;
            wp_update_post( $_POST['_tr_builtin_data'] );
            add_action( 'save_post', array( $this, 'hook' ), 1999909, 2 );
        }

        $this->savePostMeta();
    }

    protected function create()
    {
        remove_action( 'save_post', array( $this, 'hook' ) );
        $insert        = array_merge(
            $this->defaultValues,
            $_POST['_tr_builtin_data'],
            $this->staticValues
        );
        $this->item_id = wp_insert_post( $insert );
        add_action( 'save_post', array( $this, 'hook' ) );
        $this->savePostMeta();
    }

    function savePostMeta()
    {

        if (is_array( $this->fields )) :
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
