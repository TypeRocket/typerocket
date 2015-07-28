<?php
namespace TypeRocket\Models;

use TypeRocket\Http\Responders\CommentsResponder;

class CommentsModel extends Model
{

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

    public function findById($id) {
        $this->id = $id;
        $this->data = get_comment($id);
        return $this;
    }

    function create( array $fields )
    {
        $fields = $this->secureFields($fields);
        $builtin = $this->getBuiltinFields($fields);

        if ( ! empty( $builtin['comment_post_id'] ) &&
             ! empty( $builtin['comment_content'] )
        ) {
            unset( $GLOBALS['wp_filter']['wp_insert_comment']['typerocket_responder_hook'] );
            $comment = wp_new_comment( $this->formatFields( $builtin ) );
            $responder = new CommentsResponder();
            add_action( 'wp_insert_comment', array( $responder, 'respond' ), 'typerocket_responder_hook', 3 );

            if (empty( $comment ) ) {
                $message      = 'Comment not created.';
                $this->errors = array( $message );
            } else {
                $this->id = $comment;
                $this->data = get_comment($comment);
            }
        } else {
            $this->errors = array(
                'Missing post ID `comment_post_id`.',
                'Missing comment content `comment_content`.'
                );
        }

        $this->saveMeta( $fields );

        return $this;
    }

    function update( array $fields )
    {
        if($this->id == null) {
            $fields = $this->secureFields($fields);
            $builtin = $this->getBuiltinFields($fields);

            if(!empty($builtin)) {
                unset( $GLOBALS['wp_filter']['edit_comment']['typerocket_responder_hook'] );
                $fields['comment_id'] = $this->id;
                wp_update_comment( $this->formatFields( $builtin ) );
                $responder = new CommentsResponder();
                add_action( 'edit_comment', array( $responder, 'respond' ), 'typerocket_responder_hook', 3 );
            }

            $this->saveMeta( $fields );
        } else {
            $this->errors = array('No item to update');
        }

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
