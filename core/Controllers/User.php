<?php

namespace TypeRocket\Controllers;

class User extends Controller
{

    function hook( $user_id, $user )
    {
        $this->user  = $user;
        $this->valid = true;
        $this->save( $user_id );
    }

    function update()
    {
        if (isset( $_POST['_tr_builtin_data'] )) :
            $_POST['_tr_builtin_data']['ID'] = $this->item_id;
            wp_update_user( $_POST['_tr_builtin_data'] );
            unset( $this->fields['user_insert'] );
        endif;

        $this->saveMeta();
    }

    function create()
    {
        $insert        = array_merge(
            $this->defaults,
            $_POST['_tr_builtin_data'],
            $this->statics
        );
        $this->item_id = wp_insert_user( $insert );

        $this->saveMeta();
    }

    function saveMeta()
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