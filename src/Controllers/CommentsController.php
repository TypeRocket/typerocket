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
        if($this->comment === null && ! empty($item_id) ) {
            $this->comment = get_comment($item_id);
        }

        $fillable = apply_filters( 'tr_comments_controller_fillable', $this->getFillable(), $this );
        $this->setFillable($fillable);
        parent::save( $item_id, $action );

        return $this;
    }

    function saveCommentMeta()
    {
        if (is_array( $this->fields ) && ! empty($this->item_id) ) :
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

    private function setCommentUppercaseFields() {

        if(is_array($this->fieldsBuiltin)) {

            if(!empty($this->fieldsBuiltin['comment_post_id'] )) {
                $this->fieldsBuiltin['comment_post_ID'] = (int) $this->fieldsBuiltin['comment_post_id'];
                unset($this->fieldsBuiltin['comment_post_id']);
            }

            if(!empty($this->fieldsBuiltin['comment_author_ip'] )) {
                $this->fieldsBuiltin['comment_author_IP'] = $this->fieldsBuiltin['comment_author_ip'];
                unset($this->fieldsBuiltin['comment_author_ip']);
            }


        }


    }

    protected function update()
    {
        $this->setCommentUppercaseFields();

        if (is_array( $this->fieldsBuiltin )) {
            unset($GLOBALS['wp_filter']['edit_comment']['dghp278fndfluhn7']);
            $this->fieldsBuiltin['comment_ID'] = $this->item_id;
            wp_update_comment( $this->fieldsBuiltin );
            add_action( 'edit_comment', array( $this, 'hook' ), 'dghp278fndfluhn7', 3 );
        }

        $this->saveCommentMeta();

        do_action('tr_comments_controller_update', $this);

        return $this;
    }

    protected function create()
    {
        $this->setCommentUppercaseFields();

        if( ! empty($this->fieldsBuiltin['comment_post_ID']) &&
            ! empty($this->fieldsBuiltin['comment_content']) ) {
            unset($GLOBALS['wp_filter']['wp_insert_comment']['dghp278fndfluhn7']);
            $comment = wp_new_comment($this->fieldsBuiltin);
            add_action( 'wp_insert_comment', array( $this, 'hook' ), 'dghp278fndfluhn7', 3 );
        } else {
            $comment = false;
        }


        if($comment instanceof \WP_Error || ! is_int($comment)) {
            $this->response['message'] = 'Comment not created';
            $message = 'Missing post ID `comment_post_ID`.';
            $this->response['errors'] = isset($comment->errors) ? $comment->errors : array($message);
            $this->valid = false;
        } else {
            $this->item_id = $comment;
        }

        $this->saveCommentMeta();

        do_action('tr_comments_controller_create', $this);

        return $this;
    }
}
