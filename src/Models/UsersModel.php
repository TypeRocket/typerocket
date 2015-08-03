<?php
namespace TypeRocket\Models;

class UsersModel extends Model
{

    protected $builtin = array(
        'user_login',
        'user_nicename',
        'user_email',
        'user_url',
        'user_activation_key',
        'user_status',
        'display_name',
        'user_registered',
        'id',
        'user_pass'
    );

    protected $guard = array(
        'id'
    );

    public function findById( $id )
    {
        $this->id   = $id;
        $this->setData('user', get_userdata( $this->id ));

        return $this;
    }

    function create( array $fields )
    {
        $fields = $this->secureFields( $fields );
        $fields = array_merge( $this->default, $fields, $this->static );

        $builtin = $this->getFilteredBuiltinFields( $fields );

        if ( ! empty( $builtin )) {
            remove_action( 'user_register', 'TypeRocket\Http\Responders\Hook::users' );
            $user  = wp_insert_user( $builtin );
            add_action( 'user_register', 'TypeRocket\Http\Responders\Hook::users' );

            if ($user instanceof \WP_Error || ! is_int( $user )) {
                $this->errors = isset( $user->errors ) ? $user->errors : array();
            } else {
                $this->id   = $user;
                $this->setData('user', get_userdata( $this->id ));
            }
        }


        $this->saveMeta( $fields );

        return $this;
    }

    function update( array $fields )
    {
        if ($this->id != null) {
            $fields = $this->secureFields( $fields );
            $fields = array_merge( $fields, $this->static );

            $builtin = $this->getFilteredBuiltinFields( $fields );
            if ( ! empty( $builtin )) {
                remove_action( 'profile_update', 'TypeRocket\Http\Responders\Hook::users' );
                $builtin['ID'] = $this->id;
                wp_update_user( $builtin );
                add_action( 'profile_update', 'TypeRocket\Http\Responders\Hook::users' );
                $this->setData('user', get_userdata( $this->id ));
            }

            $this->saveMeta( $fields );
        } else {
            $this->errors = array( 'No item to update' );
        }

        return $this;
    }

    /**
     * Save user meta fields from TypeRocket fields
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

                $current_value = get_user_meta( $this->id, $key, true );

                if (isset( $value ) && $value !== $current_value) :
                    update_user_meta( $this->id, $key, $value );
                elseif ( ! isset( $value ) || $value === "" && ( isset( $current_value ) || $current_value === "" )) :
                    delete_user_meta( $this->id, $key );
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

        if (in_array( $field_name, $this->builtin )) {

            /** @var \WP_User $user */
            $user = $this->getData('user');

            switch ($field_name) {
                case 'id' :
                    $data = $user->ID;
                    break;
                case 'user_pass' :
                    $data = '';
                    break;
                default :
                    $data = $user->$field_name;
                    break;
            }
        } else {
            $data = get_metadata( 'user', $this->id, $field_name, true );
        }

        return $this->getValueOrNull( $data );
    }
}