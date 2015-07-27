<?php
namespace TypeRocket\Models;

use \TypeRocket\Http\Responders\PostsResponder;

class PostsModel extends Model
{

    private $id = null;
    protected $builtin = array(
        'post_author',
        'post_date',
        'post_date_gmt',
        'post_content',
        'post_title',
        'post_excerpt',
        'post_status',
        'comment_status',
        'ping_status',
        'post_name',
        'to_ping',
        'pinged',
        'post_modified',
        'post_modified_gmt',
        'post_content_filtered',
        'post_parent',
        'guid',
        'menu_order',
        'post_type',
        'post_mime_type',
        'comment_count',
        'post_password',
        'id'
    );

    public function create( array $fields )
    {
        $fields = $this->secureFields($fields);
        if ( ! empty( $fields )) {
            unset( $GLOBALS['wp_filter']['save_post']['typerocket_responder_hook'] );
            $post      = wp_insert_post( $this->getBuiltinFields($fields) );
            $responder = new PostsResponder();
            add_action( 'save_post', array( $responder, 'respond' ), 'typerocket_responder_hook' );

            if ($post instanceof \WP_Error || $post === 0) {
                $default      = 'post_name (slug), post_title, post_content, and post_excerpt are required';
                $this->errors = ! empty( $post->errors ) ? $post->errors : array( $default );
            } else {
                $this->id = $post;
            }

            $this->saveMeta( $fields );
        }

        return $this;
    }

    public function update( $itemId, array $fields )
    {
        $fields = $this->secureFields($fields);

        if ( ! empty( $fields )) {
            $this->id = (int) $itemId;

            unset( $GLOBALS['wp_filter']['save_post']['typerocket_responder_hook'] );
            $fields['ID'] = $this->id;
            wp_update_post( $this->getBuiltinFields($fields) );
            $responder = new PostsResponder();
            add_action( 'save_post', array( $responder, 'respond' ), 'typerocket_responder_hook', 3 );

            $this->saveMeta( $fields );
        }

        return $this;
    }

    private function saveMeta( array $fields )
    {
        $fields = $this->getMetaFields($fields);
        if ( ! empty($fields) && ! empty( $this->id )) :
            if ($parent_id = wp_is_post_revision( $this->id )) {
                $this->id = $parent_id;
            }

            foreach ($fields as $key => $value) :
                if (is_string( $value )) {
                    $value = trim( $value );
                }

                $current_value = get_post_meta( $this->id, $key, true );

                if (( isset( $value ) && $value !== "" ) && $value !== $current_value) :
                    update_post_meta( $this->id, $key, $value );
                elseif ( ! isset( $value ) || $value === "" && ( isset( $current_value ) || $current_value === "" )) :
                    delete_post_meta( $this->id, $key );
                endif;

            endforeach;
        endif;

    }
}
