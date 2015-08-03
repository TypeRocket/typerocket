<?php
namespace TypeRocket\Models;

class PostTypesModel extends Model
{

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
    protected $guard = array(
        'post_type',
        'id'
    );

    protected $postType = null;

    /**
     * Find post by ID
     *
     * @param $id
     *
     * @return $this
     */
    public function findById($id) {
        $this->id = $id;
        $this->setData('post', get_post( $this->id ));
        return $this;
    }

    /**
     * Create post from TypeRocket fields
     *
     * Set the post type property on extended model so they
     * are saved to the correct type. See the PagesModel
     * as example.
     *
     * @param array $fields
     *
     * @return $this
     */
    public function create( array $fields )
    {
        $fields = $this->secureFields($fields);
        $fields = array_merge($this->default, $fields, $this->static);
        $builtin = $this->getFilteredBuiltinFields($fields);

        if ( ! empty( $builtin )) {
            remove_action('save_post', 'TypeRocket\Http\Responders\Hook::posts');

            if(!empty($this->postType)) {
                $builtin['post_type'] = $this->postType;
            }

            $post      = wp_insert_post( $builtin );
            add_action('save_post', 'TypeRocket\Http\Responders\Hook::posts');

            if ( $post instanceof \WP_Error || $post === 0 ) {
                $default      = 'post_name (slug), post_title, post_content, and post_excerpt are required';
                $this->errors = ! empty( $post->errors ) ? $post->errors : array( $default );
            } else {
                $this->id   = $post;
                $this->setData('post', get_post( $this->id ));
            }
        }

        $this->saveMeta( $fields );

        return $this;
    }

    /**
     * Update post from TypeRocket fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function update( array $fields )
    {
        if($this->id != null && ! wp_is_post_revision( $this->id ) ) {
            $fields = $this->secureFields($fields);
            $fields = array_merge($fields, $this->static);
            $builtin = $this->getFilteredBuiltinFields($fields);

            if ( ! empty( $builtin ) ) {
                remove_action('save_post', 'TypeRocket\Http\Responders\Hook::posts');
                $builtin['ID'] = $this->id;
                $post = $this->getData('post');
                $builtin['post_type'] = $post->post_type;
                wp_update_post( $builtin );
                add_action('save_post', 'TypeRocket\Http\Responders\Hook::posts');
                $this->setData('post', get_post( $this->id ));
            }

            $this->saveMeta( $fields );

        } else {
            $this->errors = array('No item to update');
        }

        return $this;
    }

    /**
     * Save post meta fields from TypeRocket fields
     *
     * @param array $fields
     */
    private function saveMeta( array $fields )
    {
        $fields = $this->getFilteredMetaFields($fields);
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

        if(in_array($field_name, $this->builtin)) {
            switch ($field_name) {
                case 'post_password' :
                    $data = '';
                    break;
                case 'id' :
                    $data = get_post_field( 'ID', $this->id, 'raw' );
                    break;
                default :
                    $data = get_post_field( $field_name, $this->id, 'raw' );
                    break;
            }
        } else {
            $data = get_metadata( 'post', $this->id, $field_name, true );
        }

        return $this->getValueOrNull($data);
    }
}
