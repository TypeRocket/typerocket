<?php
namespace TypeRocket;

class Html
{

    private static $noCloseTags = array( 'img' => true, 'input' => true, 'hr' => true, 'br' => true );

    /**
     * Make HTML
     *
     * This is a function that will help you make html without playing with too much.
     * It is very helpful when you need to make dynamic html.
     *
     * $html needs to be an array within an array as follows
     *
     * @param array $html
     * @param bool $close
     *
     * @return string
     */
    static function make_html( $html, $close = true )
    {
        $output = '';
        $s      = ' ';
        $count  = count( $html );

        if (is_array( $html )) : for ($i = 0; $i < $count; $i ++) :
            foreach ($html[$i] as $tag => $attributes)  :
                $output .= self::check_none( $tag, '<' . $tag . $s );

                foreach ($attributes as $attr => $value) :
                    if ($attr != 'html') {
                        $output .= self::make_html_attr( $attr, $value ) . $s;
                    }
                endforeach;

                if (array_key_exists( $tag, self::get_close_tags() )) :
                    $output .= self::check_none( $tag, '/>' );
                else :
                    $output .= self::check_none( $tag, '>' );
                    $output .= self::check_content( $attributes['html'] );
                    if ($close) :
                        $output .= self::check_none( $tag, '</' . $tag . '>' );
                    endif;
                endif;

            endforeach;
        endfor; endif;

        return $output;
    }

    /**
     * Make Any Element
     *
     * Make an element cleans up unwanted array nesting
     *
     * @param $tag
     * @param $args
     * @param $html
     * @param bool $make
     *
     * @return array|string
     */
    static function element( $tag, $args, $html = null, $make = true )
    {
        if (isset( $html )) {
            $args = array_merge( $args, array( 'html' => $html ) );
        }

        $args = array( $tag => $args );

        if (isset( $make )) {
            return self::make_html( array( $args ) );
        } else {
            return $args;
        }
    }

    /**
     * Open Tag
     *
     * Make an element cleans up unwanted array nesting
     *
     * @param $tag
     * @param $args
     * @param $html
     * @param bool $make
     *
     * @return array|string
     */
    static function open_element( $tag, $args, $html = null, $make = true )
    {
        if (isset( $html )) {
            $args = array_merge( $args, array( 'html' => $html ) );
        }

        $args = array( $tag => $args );

        if (isset( $make )) {
            return self::make_html( array( $args ), false );
        } else {
            return $args;
        }
    }

    /**
     * Make HTML Attributes
     *
     * Check if value is null. If so skip the attr.
     *
     * @param $attr
     * @param string $value
     *
     * @return string
     */
    static function make_html_attr( $attr, $value = '' )
    {
        if ($value === true) {
            $value = $attr;
        }

        if (isset( $value )) {
            return "{$attr}=\"{$value}\"";
        } else {
            return '';
        }
    }

    /**
     * Check if there is no tag
     *
     * Set the tag value to none to create text only
     *
     * @param $tag
     * @param $output
     *
     * @return string
     */
    private static function check_none( $tag, $output )
    {
        if ($tag != 'none') {
            return $output;
        } else {
            return '';
        }
    }

    /**
     * Check HTML array value
     *
     * If the HTML value is an array add the new element.
     * If the HTML value is a string add the plain text.
     *
     * @param $html
     *
     * @return string
     */
    private static function check_content( $html )
    {
        if (is_string( $html )) :
            return $html;
        elseif (isset( $html ) && is_array( $html )) :
            return self::make_html( $html );
        else :
            return '';
        endif;
    }

    /**
     * Shortcut to create an input field
     *
     * @param string $type
     * @param string $name
     * @param string $value
     * @param array $attr
     * @param null $make
     *
     * @return array|string
     */
    static function input( $type, $name, $value = '', $attr = array(), $make = null )
    {

        $defaults = array(
            'type'  => $type,
            'name'  => $name,
            'value' => $value
        );

        $attr = array_merge( $defaults, $attr );

        return self::element( 'input', $attr, $make );
    }

    /**
     * Get Tags that do not close
     *
     * @return array
     */
    private static function get_close_tags()
    {
        return self::$noCloseTags;
    }

}