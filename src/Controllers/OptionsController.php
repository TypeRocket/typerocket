<?php
namespace TypeRocket\Controllers;

class OptionsController extends Controller
{

    function getValidate()
    {
        $this->valid = parent::getValidate();

        if( ! current_user_can( 'manage_options')) {
            $this->valid = false;
            $this->response['message'] = "Sorry, you don't have enough rights.";
        }

        if( $this->requestType != 'TypeRocketApi' && ! check_ajax_referer( 'form_' . TR_SEED, '_tr_nonce_form', false )) {
            $this->valid = false;
            $this->response['message'] = 'Invalid CSRF Token';
        }

        $this->valid = apply_filters( 'tr_options_controller_validate', $this->valid, $this );

        return $this->valid;
    }

    function filter()
    {
        parent::filter();
        $this->fields = apply_filters( 'tr_options_controller_filter', $this->fields, $this );

        return $this;
    }

    function save( $item_id, $action = 'update' )
    {
        $fillable = apply_filters( 'tr_options_controller_fillable', $this->getFillable(), $this );
        $this->setFillable($fillable);
        parent::save( $item_id, $action );

        return $this;
    }

    protected function addOptions()
    {
        if (is_array( $this->fields )) :
            foreach ($this->fields as $key => $value) :

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
        endif;
    }

    protected function update()
    {
        $this->addOptions();

        do_action('tr_options_controller_update', $this);

        return $this;
    }

    protected function create()
    {
        $this->addOptions();

        do_action('tr_options_controller_create', $this);

        return $this;
    }

}
