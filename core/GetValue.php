<?php
namespace TypeRocket;

class GetValue
{

    public function getFromBrackets( $brackets, $item_id, $controller, $builtin = false )
    {
        $keys = $this->geBracketKeys( $brackets );
        $data = $this->controllerSwitch( $keys[0], $item_id, $controller, $builtin );

        return $this->parseValueData( $data, $keys );
    }


    /**
     * @param \TypeRocket\Fields\Field $field
     *
     * @return string|false
     */
    public function getFromField( $field )
    {
        $brackets = $field->getBrackets();
        $item_id =$field->getItemID();
        $controller = $field->getController();
        $builtin = $field->getBuiltin();
        return $this->getFromBrackets( $brackets, $item_id, $controller, $builtin);
    }

    private function parseValueData( $data, $keys )
    {
        if (isset( $keys[1] ) && ! empty( $data )) {
            $data = maybe_unserialize( $data );

            // unset first key since $data is already set to it
            unset( $keys[0] );

            if ( ! empty( $keys ) && is_array( $keys )) {
                foreach ($keys as $name) {
                    $data = ( isset( $data[$name] ) && $data[$name] !== '') ? $data[$name] : null;
                }
            }

        }

        $data = $this->decode( $data );

        return $data;
    }

    private function decode($v) {
        if (is_string($v)) {
            $v = wp_unslash($v);
        } elseif (is_array($v)) {
            $v = stripslashes_deep($v);
        }

        return $v;
    }

    private function controllerSwitch( $the_field, $item_id, $controller, $builtin )
    {
        switch ($controller) {
            case 'posts' :
                if ($builtin == true) {
                    $data = get_post_field( $the_field, $item_id, 'raw' );
                } else {
                    $data = get_metadata( 'post', $item_id, $the_field, true );
                }
                break;
            case 'users' :
                if ($builtin == true) {
                    $data = $this->getUserData( $item_id, $the_field );
                } else {
                    $data = get_metadata( 'user', $item_id, $the_field, true );
                }
                break;
            case 'comments' :
                $data = get_metadata( 'comment', $item_id, $the_field, true );
                break;
            case 'options' :
                $data = get_option( $the_field );
                break;
            default :
                $func = 'tr_get_data_' . $controller;
                $data = call_user_func( $func, $controller, $item_id, $the_field );
                break;
        }

        $data = apply_filters( 'tr_field_data_filter', $data, $this, $the_field, $item_id, $controller, $builtin );

        return $data !== '' ? $data : null;
    }

    private function geBracketKeys( $str, $set = 1 )
    {
        $regex = '/\[([^]]+)\]/i';
        preg_match_all( $regex, $str, $matches, PREG_PATTERN_ORDER );

        return $matches[$set];
    }

    private function getUserData( $item_id, $the_field )
    {
        switch ($the_field) {
            case 'user_login' :
            case 'user_nicename' :
            case 'user_email' :
            case 'user_url' :
            case 'display_name' :
            case 'user_registered' :
                $data = get_userdata( $item_id );
                $data = $data->$the_field;
                break;
            case 'user_pass' :
                $data = '';
                break;
            default :
                $data = get_user_meta( $item_id, $the_field, true );
                break;
        }

        return $data;

    }

}