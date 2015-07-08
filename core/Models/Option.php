<?php

namespace TypeRocket\Models;

class Option extends Model {

    function hook( $item_id, $post )
    {
        $this->valid = true;
        $this->save( $item_id, $action = 'update' );
    }

    function validate()
    {
        $this->valid = apply_filters( 'tr_post_validate', $this->valid, $this );

        return ( $this->valid && $this->post instanceof \WP_Post );
    }

    function sanitize()
    {
        $this->fields = apply_filters( 'tr_post_sanitize', $_POST['tr'], $this );
    }

    protected function save($item_id, $action = 'update') {
        $this->saveOptions();
    }

    private function saveOptions() {
        if ( is_array( $this->fields ) ) :
            foreach ( $this->fields as $key => $value ) :

                if ( is_string( $value ) ) {
                    $value = trim( $value );
                }

                $current_meta = get_option( $key );

                if ( ( isset( $value ) && $value !== "" ) && $current_meta !== $value ) :
                    update_option( $key, $value );
                elseif ( ! isset( $value ) || $value === "" && ( isset( $current_meta ) || $current_meta === "" ) ) :
                    delete_option( $key );
                endif;

            endforeach;
        endif;
    }
}