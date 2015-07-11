<?php

namespace TypeRocket\Controllers;

class User extends Controller
{

    /** @var \WP_User */
    public $user = null;

    function hook( $user_id )
    {
        $this->user  = get_user_by( 'id', $user_id );
        $this->valid = true;
        $this->save( $user_id );
    }

    function validate() {
        parent::validate();

        $cant_edit = ( $this->user->ID != $this->current_user->ID && ! current_user_can( 'edit_users' ) );

        if (  $cant_edit ) {
            $this->valid = false;
            $this->response['message'] = "Sorry, you don't have enough rights.";
        }

        $this->valid = apply_filters( 'tr_user_validate', $this->valid, $this );

        return $this->valid;
    }

    function sanitize()
    {
        parent::sanitize();
        $this->fields = apply_filters( 'tr_user_sanitize', $_POST['tr'], $this );
    }

    function update()
    {
        if (isset( $_POST['_tr_builtin_data'] )) :
            $_POST['_tr_builtin_data']['ID'] = $this->item_id;
            wp_update_user( $_POST['_tr_builtin_data'] );
            unset( $this->fields['user_insert'] );
        endif;

        $this->saveUserMeta();
    }

    function create()
    {
        $insert        = array_merge(
            $this->defaultValues,
            $_POST['_tr_builtin_data'],
            $this->staticValues
        );
        $this->item_id = wp_insert_user( $insert );

        $this->saveUserMeta();
    }

    function saveUserMeta()
    {
        if (is_array( $this->fields )) :
            foreach ($this->fields as $key => $value) :
                if (is_string( $value )) {
                    $value = trim( $value );
                }

                $current_value = get_user_meta( $this->item_id, $key, true );

                if (isset( $value ) && $value !== $current_value) :
                    update_user_meta( $this->item_id, $key, $value );
                elseif ( ! isset( $value ) || $value === "" && ( isset( $current_value ) || $current_value === "" )) :
                    delete_user_meta( $this->item_id, $key );
                endif;

            endforeach;
        endif;
    }
}