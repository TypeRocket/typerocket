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
     *
     * @return array|mixed|null|string
     */
    public function getFromBrackets( $brackets, $item_id, $controller )
    {

        if ($item_id === null && $controller !== 'options') {
            return null;
        }

        $keys = $this->geBracketKeys( $brackets );
        $data = $this->controllerSwitch( $keys[0], $item_id, $controller );

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
        $item_id    = $field->getItemID();
        $brackets   = $field->getBrackets();
        $controller = $field->getController();

        return $this->getFromBrackets( $brackets, $item_id, $controller );
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
        $mainKey = $keys[0];
        if (isset( $mainKey ) && ! empty( $data )) {

            if (is_serialized( $data )) {
                $data = unserialize( $data );
            }

            // unset first key since $data is already set to it
            unset( $keys[0] );

            if ( ! empty( $keys ) && is_array( $keys )) {
                foreach ($keys as $name) {
                    $data = ( isset( $data[$name] ) && $data[$name] !== '' ) ? $data[$name] : null;
                }
            }

        }

        return $data;
    }

    /**
     * Get data from correct source in WordPress
     *
     * @param $the_field
     * @param $item_id
     * @param $controller
     *
     * @return mixed|null|void
     */
    private function controllerSwitch( $the_field, $item_id, $controller )
    {
        $data = null;

        switch ($controller) {
            case 'posts' :
                $data = $this->getPostsData( $item_id, $the_field );
                break;
            case 'users' :
                $data = $this->getUsersData( $item_id, $the_field );
                break;
            case 'comments' :
                $data = $this->getCommentsData( $item_id, $the_field );
                break;
            case 'options' :
                $data = get_option( $the_field );
                break;
            default :
                $func = 'tr_get_data_' . $controller;
                if (function_exists( $func )) {
                    $data = call_user_func( $func, $item_id, $the_field );
                } else {
                    echo( 'TypeRocket: Create a custom controller <code>function ' . $func . '($controller, $item_id, $the_field) { return $data; }</code>' );
                    exit();
                }
                break;
        }

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
    private function getUsersData( $item_id, $the_field )
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
            case 'id' :
                $data = get_userdata( $item_id );
                $data = $data->ID;
                break;
            case 'user_pass' :
                $data = '';
                break;
            default :
                $data = get_metadata( 'user', $item_id, $the_field, true );
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
    private function getCommentsData( $item_id, $the_field )
    {

        switch ($the_field) {
            case 'comment_author' :
            case 'comment_author_email' :
            case 'comment_author_url' :
            case 'comment_type' :
            case 'comment_parent' :
            case 'user_id' :
            case 'comment_date' :
            case 'comment_date_gmt' :
            case 'comment_content' :
            case 'comment_karma' :
            case 'comment_approved' :
            case 'comment_agent' :
                $comment = get_comment( $item_id );
                $data    = $comment->$the_field;
                break;
            case 'comment_author_ip' :
                $comment = get_comment( $item_id );
                $data    = $comment->comment_author_IP;
                break;
            case 'comment_post_id' :
                $comment = get_comment( $item_id );
                $data    = $comment->comment_post_ID;
                break;
            case 'comment_id' :
                $comment = get_comment( $item_id );
                $data    = $comment->comment_ID;
                break;
            default :
                $data = get_metadata( 'comment', $item_id, $the_field, true );
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
    private function getPostsData( $item_id, $the_field )
    {

        switch ($the_field) {
            case 'post_author' :
            case 'post_date' :
            case 'post_date_gmt' :
            case 'post_content' :
            case 'post_title' :
            case 'post_excerpt' :
            case 'post_status' :
            case 'comment_status' :
            case 'ping_status' :
            case 'post_name' :
            case 'to_ping' :
            case 'pinged' :
            case 'post_modified' :
            case 'post_modified_gmt' :
            case 'post_content_filtered' :
            case 'post_parent' :
            case 'guid' :
            case 'menu_order' :
            case 'post_type' :
            case 'post_mime_type' :
            case 'comment_count' :
                $data = get_post_field( $the_field, $item_id, 'raw' );
                break;
            case 'post_password' :
                $data = '';
                break;
            case 'id' :
                $data = get_post_field( 'ID', $item_id, 'raw' );
                break;
            default :
                $data = get_metadata( 'post', $item_id, $the_field, true );
                break;
        }

        return $data;

    }

}