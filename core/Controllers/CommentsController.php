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
        $this->fields = apply_filters( 'tr_comment_controller_filter', $_POST['tr'], $this );

        return $this;
    }

    function save( $item_id, $action = 'update' )
    {
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
        $this->saveCommentMeta();

        return $this;
    }

    protected function create()
    {
        return $this;
    }
}