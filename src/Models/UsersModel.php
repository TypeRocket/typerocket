<?php
namespace TypeRocket\Models;

use TypeRocket\Http\Responders\UsersResponder;

class UsersModel extends Model
{
    /** @var \WP_User  */
    protected $data = null;
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

    public function findById($id) {
        $this->id = $id;
        $this->data = get_user_by( 'ID', $id );
        return $this;
    }

    function create( array $fields )
    {
        $fields = $this->secureFields($fields);
        $builtin = $this->getBuiltinFields($fields);

        if(!empty($builtin)) {
            unset( $GLOBALS['wp_filter']['user_register']['typerocket_responder_hook'] );
            $user = wp_insert_user( $this->getBuiltinFields($fields) );
            $users = new UsersResponder();
            add_action( 'user_register', array( $users, 'respond' ), 'typerocket_responder_hook', 3 );

            if ($user instanceof \WP_Error || ! is_int( $user )) {
                $this->errors = isset( $user->errors ) ? $user->errors : array();
            } else {
                $this->id = $user;
                $this->data = get_user_by( 'ID', $user );
            }
        }


        $this->saveMeta( $fields );

        return $this;
    }

    function update( array $fields )
    {
        if($this->id != null) {
            $fields = $this->secureFields($fields);
            $builtin = $this->getBuiltinFields($fields);
            if(!empty($builtin)) {
                $fields['ID'] = $this->id;
                unset( $GLOBALS['wp_filter']['profile_update']['typerocket_responder_hook'] );
                wp_update_user( $this->getBuiltinFields($fields) );
                $users = new UsersResponder();
                add_action( 'profile_update', array( $users, 'respond' ), 'typerocket_responder_hook', 3 );
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

                $current_value = get_user_meta( $this->id, $key, true );

                if (isset( $value ) && $value !== $current_value) :
                    update_user_meta( $this->id, $key, $value );
                elseif ( ! isset( $value ) || $value === "" && ( isset( $current_value ) || $current_value === "" )) :
                    delete_user_meta( $this->id, $key );
                endif;

            endforeach;
        endif;
    }
}