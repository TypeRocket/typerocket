<?php
namespace TypeRocket\Models;

use TypeRocket\Http\Responders\CommentsResponder;

class CommentsModel extends Model
{

    private $id = null;
    protected $builtin = array(
        'comment_author',
        'comment_author_email',
        'comment_author_url',
        'comment_type',
        'comment_parent',
        'user_id',
        'comment_date',
        'comment_date_gmt',
        'comment_content',
        'comment_karma',
        'comment_approved',
        'comment_agent',
        'comment_author_ip',
        'comment_post_id',
        'comment_id'
    );

    function create( array $fields )
    {
        $fields = $this->formatFields( $fields );

        if ( ! empty( $fields['comment_post_ID'] ) &&
             ! empty( $fields['comment_content'] )
        ) {
            unset( $GLOBALS['wp_filter']['wp_insert_comment']['typerocket_responder_hook'] );
            $comment = wp_new_comment( $this->getBuiltinFields($fields) );
            $responder = new CommentsResponder();
            add_action( 'wp_insert_comment', array( $responder, 'respond' ), 'typerocket_responder_hook', 3 );
        } else {
            $comment = false;
        }

        if ($comment instanceof \WP_Error || ! is_int( $comment )) {
            $message      = 'Missing post ID `comment_post_ID`.';
            $this->errors = isset( $comment->errors ) ? $comment->errors : array( $message );
        } else {
            $this->id = $comment;
        }

        $this->saveMeta( $fields );

        return $this;
    }

    function update( $itemId, array $fields )
    {
        unset( $GLOBALS['wp_filter']['edit_comment']['typerocket_responder_hook'] );
        $fields['comment_id'] = $itemId;
        wp_update_comment( $this->formatFields( $this->getBuiltinFields($fields) ) );
        $responder = new CommentsResponder();
        add_action( 'edit_comment', array( $responder, 'respond' ), 'typerocket_responder_hook', 3 );

        $this->saveMeta( $fields );

        return $this;
    }

    private function saveMeta( array $fields )
    {
        $fields = $this->getMetaFields($fields);
        if ( ! empty($fields) && ! empty( $this->id )) :
            foreach ($fields as $key => $value) :
                if (is_string( $value )) {
                    $value = trim( $value );
                }

                $current_value = get_comment_meta( $this->id, $key, true );

                if (( isset( $value ) && $value !== "" ) && $value !== $current_value) :
                    update_comment_meta( $this->id, $key, $value );
                elseif ( ! isset( $value ) || $value === "" && ( isset( $current_value ) || $current_value === "" )) :
                    delete_comment_meta( $this->id, $key );
                endif;

            endforeach;
        endif;
    }

    private function formatFields( array $fields )
    {

        if ( ! empty( $fields['comment_post_id'] )) {
            $fields['comment_post_ID'] = (int) $fields['comment_post_id'];
            unset( $fields['comment_post_id'] );
        }

        if ( ! empty( $fields['comment_id'] )) {
            $fields['comment_ID'] = (int) $fields['comment_id'];
            unset( $fields['comment_id'] );
        }

        if ( ! empty( $fields['comment_author_ip'] )) {
            $fields['comment_author_IP'] = $fields['comment_author_ip'];
            unset( $fields['comment_author_ip'] );
        }

        return $fields;

    }
}
