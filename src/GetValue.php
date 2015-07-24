<?php
namespace TypeRocket;

class GetValue
{

    /**
     * Get value from database from typeRocket bracket syntax
     *
     * @param $brackets
     * @param $item_id
     * @param $controller
     * @param bool|false $builtin
     *
     * @return array|mixed|null|string
     */
    public function getFromBrackets( $brackets, $item_id, $controller, $builtin = false )
    {
        $keys = $this->geBracketKeys( $brackets );
        $data = $this->controllerSwitch( $keys[0], $item_id, $controller, $builtin );

        return $this->parseValueData( $data, $keys );
    }


    /**
     * Get value from Field object
     *
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

    /**
     * Parse data by walking through keys
     *
     * @param $data
     * @param $keys
     *
     * @return array|mixed|null|string
     */
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

    /**
     * Decode data by removing slashes
     *
     * @param $v
     *
     * @return array|mixed|string
     */
    private function decode($v) {
        if (is_string($v)) {
            $v = wp_unslash($v);
        } elseif (is_array($v)) {
            $v = stripslashes_deep($v);
        }

        return $v;
    }

    /**
     * Get data from correct source in WordPress
     *
     * @param $the_field
     * @param $item_id
     * @param $controller
     * @param $builtin
     *
     * @return mixed|null|void
     */
    private function controllerSwitch( $the_field, $item_id, $controller, $builtin )
    {
        $data = null;

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
                $data = $this->getCommentData( $item_id, $the_field );
                break;
            case 'options' :
                $data = get_option( $the_field );
                break;
            default :
                $func = 'tr_get_data_' . $controller;
                if(function_exists($func)) {
                    $data = call_user_func( $func, $controller, $item_id, $the_field );
                } else {
                    echo('TypeRocket: Create a custom controller <code>function '. $func . '($controller, $item_id, $the_field) { return $data; }</code>');
                    exit();
                }
                break;
        }

        $data = apply_filters( 'tr_field_data_filter', $data, $this, $the_field, $item_id, $controller, $builtin );

        return $data !== '' ? $data : null;
    }

    /**
     * Get keys from TypeRocket brackets
     *
     * @param $str
     * @param int $set
     *
     * @return mixed
     */
    private function geBracketKeys( $str, $set = 1 )
    {
        $regex = '/\[([^]]+)\]/i';
        preg_match_all( $regex, $str, $matches, PREG_PATTERN_ORDER );

        return $matches[$set];
    }

    /**
     * Getting user data is a little more complicated. Use this to get the correct data.
     *
     * @param $item_id
     * @param $the_field
     *
     * @return bool|mixed|string|\WP_User
     */
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

    /**
     * Get comment data
     *
     * @param $item_id
     * @param $the_field
     *
     * @return mixed
     */
    private function getCommentData($item_id, $the_field) {

        switch ($the_field) {
            case 'comment_post_ID' :
            case 'comment_author' :
            case 'comment_author_email' :
            case 'comment_author_url' :
            case 'comment_type' :
            case 'comment_parent' :
            case 'user_id' :
            case 'comment_author_IP' :
            case 'comment_date' :
            case 'comment_date_gmt' :
            case 'comment_content' :
            case 'comment_karma' :
            case 'comment_approved' :
            case 'comment_agent' :
                $comment = get_comment( $item_id );
                $data = $comment->$the_field;
                break;
            default :
                $data = get_metadata( 'comment', $item_id, $the_field, true );
                break;
        }

        return $data;

    }

}