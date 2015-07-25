<?php
namespace TypeRocket\Controllers;

class CommentsController extends Controller
{

    public $comment = null;

    function hook( $id )
    {
        $this->item_id = $id;
        $this->comment = get_comment( $id );
        $this->valid   = true;
        $this->save( $id );
    }

    function getValidate()
    {
        $this->valid = parent::getValidate();

        if ( $this->comment->user_id != $this->currentUser->ID && ! current_user_can( 'edit_comment' ) ) {
            $this->valid = false;
            $this->response['message'] = "Sorry, you don't have enough rights.";
        }

        $this->valid = apply_filters( 'tr_comment_controller_validate', $this->valid, $this );

        return $this->valid;
    }

    function filter()
    {
        parent::filter();
        $this->fields = apply_filters( 'tr_comment_controller_filter', $this->fields, $this );

        return $this;
    }

    function save( $item_id, $action = 'update' )
    {
        $fillable = apply_filters( 'tr_comments_controller_fillable', $this->getFillable(), $this );
        $this->setFillable($fillable);
        parent::save( $item_id, $action );

        return $this;
    }

    function saveCommentMeta()
    {
        if (is_array( $this->fields )) :
            foreach ($this->fields as $key => $value) :
                if (is_string( $value )) {
                    $value = trim( $value );
                }

                $current_value = get_comment_meta( $this->item_id, $key, true );

                if (( isset( $value ) && $value !== "" ) && $value !== $current_value) :
                    update_comment_meta( $this->item_id, $key, $value );
                elseif ( ! isset( $value ) || $value === "" && ( isset( $current_value ) || $current_value === "" )) :
                    delete_comment_meta( $this->item_id, $key );
                endif;

            endforeach;
        endif;

        return $this;
    }

    protected function update()
    {

        if (is_array( $this->fieldsBuiltin )) {
            remove_action( 'wp_insert_comment', array( $this, 'hook' ), 1999909 );
            $this->fieldsBuiltin['comment_ID'] = $this->item_id;
            wp_update_comment( $this->fieldsBuiltin );
            add_action( 'wp_insert_comment', array( $this, 'hook' ), 1999909, 3 );
        }

        $this->saveCommentMeta();

        do_action('tr_comments_controller_update', $this);

        return $this;
    }

    protected function create()
    {
        remove_action( 'wp_insert_comment', array( $this, 'hook' ) );
        $insert        = array_merge(
            $this->defaultValues,
            $this->fieldsBuiltin,
            $this->staticValues
        );
        $this->item_id = wp_new_comment( $insert );
        add_action( 'wp_insert_comment', array( $this, 'hook' ) );

        $this->saveCommentMeta();

        do_action('tr_comments_controller_create', $this);

        return $this;
    }
}
