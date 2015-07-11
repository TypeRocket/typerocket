<?php

namespace TypeRocket\Controllers;

class Comment extends Controller
{

    public $comment = null;

    function hook( $id )
    {
        $this->item_id = $id;
        $this->comment = get_comment( $id );
        $this->valid   = true;
        $this->save( $id );
    }

    function validate()
    {
        parent::validate();
        $this->valid = apply_filters( 'tr_comment_validate', $this->valid, $this );

        if ( ! current_user_can( 'edit_comment', $this->item_id )) {
            $this->valid = false;
            $this->response['message'] = "Sorry, you don't have enough rights.";
        }

        return $this->valid;
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