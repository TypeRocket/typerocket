<?php
namespace TypeRocket;

class Validate
{

    /**
     * Validate a numeric value
     *
     * Numeric, decimal passes.
     *
     * @param $num
     *
     * @return bool
     */
    public static function numeric( $num )
    {
        return is_numeric( $num );
    }

    /**
     * Validate a Digit
     *
     * Digits only, no dots, passes.
     *
     * @param $digit
     *
     * @return bool
     */
    public static function digits( $digit )
    {
        return ! preg_match( "/[^0-9]/", $digit );
    }

    /**
     * Validate Bracket Syntax
     *
     * For post input name groups. Used for getting values of ACPT forms.
     *
     * @param $group
     *
     * @return int
     */
    public static function bracket( $group )
    {
        return preg_match( "/^\[.+\]/", $group );
    }

    /**
     * Is a given string a color formatted in hexadecimal notation?
     *
     * @param string $hex
     *
     * @return bool
     *
     */
    public static function hex( $hex )
    {
        $hex = trim( $hex );
        /* Strip recognized prefixes. */
        if (0 === strpos( $hex, '#' )) {
            $hex = substr( $hex, 1 );
        } elseif (0 === strpos( $hex, '%23' )) {
            $hex = substr( $hex, 3 );
        }
        /* Regex match. */
        if (0 === preg_match( '/^[0-9a-fA-F]{6}$/', $hex )) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Validate a url
     *
     * @param $url
     *
     * @return mixed
     */
    public static function url( $url )
    {
        return filter_var( $url, FILTER_VALIDATE_URL );
    }
}
