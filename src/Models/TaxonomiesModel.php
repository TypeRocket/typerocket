<?php
namespace TypeRocket\Models;

class TaxonomiesModel extends Model
{

    protected $taxonomy = null;

    protected $builtin = array(
        'description',
        'name',
        'slug',
        'parent'
    );

    protected $guard = array(
        'term_id',
        'term_taxonomy_id',
        'taxonomy',
        'term_group',
        'parent',
        'count',
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
        $this->setData('term', get_term( $this->id, $this->taxonomy ) );

        return $this;
    }

    /**
     * Create term from TypeRocket fields
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

        if ( ! empty( $builtin ) ) {
            remove_action('create_term', 'TypeRocket\Http\Responders\Hook::taxonomy');

            $name = $builtin['name'];
            unset($builtin['name']);
            $term = wp_insert_term( $name, $this->taxonomy, $builtin );
            add_action('create_term', 'TypeRocket\Http\Responders\Hook::taxonomy');

            if ( $term instanceof \WP_Error || $term === 0 ) {
                $default      = 'name is required';
                $this->errors = ! empty( $term->errors ) ? $term->errors : array( $default );
            } else {
                $this->id   = $term;
                $this->setData('term', get_term( $this->id, $this->taxonomy ) );
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
     * Save term meta fields from TypeRocket fields
     *
     * @param array $fields
     */
    private function saveMeta( array $fields )
    {
        $fields = $this->getFilteredMetaFields($fields);
        if ( ! empty($fields) && ! empty( $this->id ) ) :
            foreach ($fields as $key => $value) :
                if (is_string( $value )) {
                    $value = trim( $value );
                }

                $current_value = get_term_meta( $this->id, $key, true );

                if (( isset( $value ) && $value !== "" ) && $value !== $current_value) :
                    update_term_meta( $this->id, $key, $value );
                elseif ( ! isset( $value ) || $value === "" && ( isset( $current_value ) || $current_value === "" )) :
                    delete_term_meta( $this->id, $key );
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
                case 'term_id' :
                    /** @var \WP_Term $term */
                    $term = $this->getData('term');
                    $data = $term->term_id;
                    break;
                default :
                    $data = get_term_meta( $field_name, $this->id, 'raw' );
                    break;
            }
        } else {
            $data = get_metadata( 'term', $this->id, $field_name, true );
        }

        return $this->getValueOrNull($data);
    }

}
