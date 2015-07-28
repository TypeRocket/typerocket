<?php
namespace TypeRocket\Models;

class OptionsModel extends Model
{

    public function findById($id) {

        return $this;
    }

    function create( array $fields )
    {
        $fields = $this->secureFields( $fields );
        $fields = array_merge($this->default, $fields, $this->static);
        $this->saveOptions( $fields );

        return $this;
    }

    function update( array $fields )
    {
        $fields = $this->secureFields( $fields );
        $fields = array_merge($fields, $this->static);
        $this->saveOptions( $fields );

        return $this;
    }

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

    protected function getBaseFieldValue( $field_name )
    {
        $data = get_option( $field_name );
        return $this->getValueOrNull($data);
    }
}