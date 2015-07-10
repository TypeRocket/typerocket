<?php

namespace TypeRocket\Controllers;

class Post extends Controller
{

    public $post = null;

    function hook( $post_id, $post )
    {
        $this->post  = $post;
        $this->valid = true;
        $this->save( $post_id );
    }

    function validate()
    {
        parent::validate();
        $this->valid = apply_filters( 'tr_post_validate', $this->valid, $this );

        return $this->valid;
    }

    function sanitize()
    {
        parent::sanitize();
        $this->fields = apply_filters( 'tr_post_sanitize', $_POST['tr'], $this );
    }

    /**
     * @param $item_id
     * @param string $action
     *
     * @return Post $this
     */
    function save( $item_id, $action = 'update' )
    {
        parent::save( $item_id, $action );

        return $this;
    }

    protected function update()
    {
        if (isset( $_POST['_tr_builtin_data'] )) {
            remove_action( 'save_post', array( $this, 'hook' ), 1999909 );
            $_POST['_tr_builtin_data']['ID'] = $this->item_id;
            wp_update_post( $_POST['_tr_builtin_data'] );
            add_action( 'save_post', array( $this, 'hook' ), 1999909, 2 );
        }

        $this->saveMeta();
    }

    protected function create()
    {
        remove_action( 'save_post', array( $this, 'hook' ) );
        $insert        = array_merge(
            $this->defaultValues,
            $_POST['_tr_builtin_data'],
            $this->staticValues
        );
        $this->item_id = wp_insert_post( $insert );
        add_action( 'save_post', array( $this, 'hook' ) );
        $this->saveMeta();
    }

    function saveMeta()
    {

        if (is_array( $this->fields )) :
            if ($parent_id = wp_is_post_revision( $this->item_id )) {
                $this->item_id = $parent_id;
            }

            foreach ($this->fields as $key => $value) :
                if (is_string( $value )) {
                    $value = trim( $value );
                }

                $current_value = get_post_meta( $this->item_id, $key, true );

                if (( isset( $value ) && $value !== "" ) && $value !== $current_value) :
                    update_post_meta( $this->item_id, $key, $value );
                elseif ( ! isset( $value ) || $value === "" && ( isset( $current_value ) || $current_value === "" )) :
                    delete_post_meta( $this->item_id, $key );
                endif;

            endforeach;
        endif;
    }
}