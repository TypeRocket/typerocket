<?php
namespace TypeRocket\Models;

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

    protected $guard = array(
        'comment_id'
    );

    /**
     * Get comment by ID
     *
     * @param $id
     *
     * @return $this
     */
    public function findById( $id )
    {
        $this->id   = $id;
        $this->setData('comment', get_comment( $this->id ) );

        return $this;
    }

    /**
     * Create comment from TypeRocket fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function create( array $fields )
    {
        $fields  = $this->secureFields( $fields );
        $fields  = array_merge( $this->default, $fields, $this->static );
        $builtin = $this->getFilteredBuiltinFields( $fields );

        if ( ! empty( $builtin['comment_post_id'] ) &&
             ! empty( $builtin['comment_content'] )
        ) {
            remove_action( 'wp_insert_comment', 'TypeRocket\Http\Responders\Hook::comments' );
            $comment   = wp_new_comment( $this->formatFields( $builtin ) );
            add_action( 'wp_insert_comment', 'TypeRocket\Http\Responders\Hook::comments' );

            if (empty( $comment )) {
                $message      = 'Comment not created.';
                $this->errors = array( $message );
            } else {
                $this->id   = $comment;
                $this->setData('comment', get_comment( $this->id ) );
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

    /**
     * Update comment from TypeRocket fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function update( array $fields )
    {
        if ($this->id != null) {
            $fields  = $this->secureFields( $fields );
            $fields  = array_merge( $fields, $this->static );
            $builtin = $this->getFilteredBuiltinFields( $fields );

            if ( ! empty( $builtin )) {
                remove_action( 'edit_comment', 'TypeRocket\Http\Responders\Hook::comments' );
                $builtin['comment_id'] = $this->id;
                $builtin = $this->formatFields( $builtin );
                wp_update_comment( $builtin );
                add_action( 'edit_comment', 'TypeRocket\Http\Responders\Hook::comments' );
                $this->setData('comment', get_comment( $this->id ) );
            }

            $this->saveMeta( $fields );
        } else {
            $this->errors = array( 'No item to update' );
        }

        return $this;
    }

    /**
     * Save comment meta fields from TypeRocket fields
     *
     * @param array $fields
     */
    private function saveMeta( array $fields )
    {
        $fields = $this->getFilteredMetaFields( $fields );
        if ( ! empty( $fields ) && ! empty( $this->id )) :
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

    /**
     * Format irregular fields
     *
     * @param array $fields
     *
     * @return array
     */
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

    /**
     * Get base field value
     *
     * Some fields need to be saved as serialized arrays. Getting
     * the field by the base value is used by Fields to populate
     * their values.
     *
     * @param $field_name
     *
     * @return null
     */
    protected function getBaseFieldValue( $field_name )
    {
        if (in_array( $field_name, $this->builtin )) {

            $comment = $this->getData('comment');

            switch ($field_name) {
                case 'comment_author_ip' :
                    $data = $comment->comment_author_IP;
                    break;
                case 'comment_post_id' :
                    $data = $comment->comment_post_ID;
                    break;
                case 'comment_id' :
                    $data = $comment->comment_ID;
                    break;
                default :
                    $data = $comment->$field_name;
                    break;
            }

        } else {
            $data = get_metadata( 'comment', $this->id, $field_name, true );
        }

        return $this->getValueOrNull( $data );
    }

}
