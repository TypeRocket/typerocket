<?php
namespace TypeRocket\Models;

class OptionsModel extends Model
{

    protected $id = 1;

    /**
     * Do nothing since options are not true resources
     *
     * @param $id
     *
     * @return $this
     */
    public function findById($id) {

        return $this;
    }

    /**
     * Create options from TypeRocket fields
     *
     * @param array $fields
     *
     * @return $this
     */
    function create( array $fields )
    {
        $fields = $this->secureFields( $fields );
        $fields = array_merge($this->default, $fields, $this->static);
        $this->saveOptions( $fields );

        return $this;
    }

    /**
     * Update options from TypeRocket fields
     *
     * @param array $fields
     *
     * @return $this
     */
    function update( array $fields )
    {
        $fields = $this->secureFields( $fields );
        $fields = array_merge($fields, $this->static);
        $this->saveOptions( $fields );

        return $this;
    }

    /**
     * Save options' fields from TypeRocket fields
     *
     * @param array $fields
     */
    private function saveOptions( array $fields )
    {
        if ( ! empty( $fields )) {
            foreach ($fields as $key => $value) :

                if (is_string( $value )) {
                    $value = trim( $value );
                }

                $current_meta = get_option( $key );

                if (( isset( $value ) && $value !== "" ) && $current_meta !== $value) :
                    update_option( $key, $value );
                elseif ( ! isset( $value ) || $value === "" && ( isset( $current_meta ) || $current_meta === "" )) :
                    delete_option( $key );
                endif;

            endforeach;
        }

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
        $data = get_option( $field_name );
        return $this->getValueOrNull($data);
    }
}