<?php
namespace TypeRocket\Controllers;

class UsersController extends Controller
{

    /** @var \WP_User */
    public $user = null;

    function hook( $user_id )
    {
        $this->user  = get_user_by( 'id', $user_id );
        $this->valid = true;
        $this->save( $user_id );
    }

    function getValidate()
    {
        $this->valid = parent::getValidate();

        $cant_edit = ( $this->user->ID != $this->currentUser->ID && ! current_user_can( 'edit_users' ) );

        if ($cant_edit) {
            $this->valid               = false;
            $this->response['message'] = "Sorry, you don't have enough rights.";
        }

        $this->valid = apply_filters( 'tr_users_controller_validate', $this->valid, $this );

        return $this->valid;
    }

    function filter()
    {
        parent::filter();
        $this->fields = apply_filters( 'tr_users_controller_filter', $this->fields, $this );
    }

    /**
     * @param $item_id
     * @param string $action
     *
     * @return PostsController $this
     */
    function save( $item_id, $action = 'update' )
    {

        if($this->user === null && ! empty($item_id) ) {
            $this->user = get_user_by( 'id', $item_id );
        }

        $fillable = apply_filters( 'tr_users_controller_fillable', $this->getFillable(), $this );
        $this->setFillable($fillable);
        parent::save( $item_id, $action );

        return $this;
    }

    function update()
    {
        if (is_array( $this->fieldsBuiltin )) {
            $this->fieldsBuiltin['ID'] = $this->item_id;
            wp_update_user( $this->fieldsBuiltin );
            unset( $this->fields['user_insert'] );
        }

        $this->saveUserMeta();

        do_action('tr_users_controller_update', $this);

        return $this;
    }

    function create()
    {

        $user = wp_insert_user( $this->fieldsBuiltin );

        if($user instanceof \WP_Error || ! is_int($user) ) {
            $this->response['message'] = 'User not created';
            $this->response['errors'] = isset($user->errors) ? $user->errors : array();
            $this->valid = false;
        } else {
            $this->item_id = $user;
        }

        $this->saveUserMeta();

        do_action('tr_users_controller_create', $this);

        return $this;
    }

    function saveUserMeta()
    {
        if (is_array( $this->fields ) && ! empty($this->item_id) ) :
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
