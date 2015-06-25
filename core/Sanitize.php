<?php
namespace TypeRocket;

class Sanitize {

  /**
   * Sanitize a textarea input field. Removes bad html like <script> and <html>.
   *
   * @param $input
   *
   * @return string
   */
  static function textarea($input) {
    global $allowedposttags;
    $output = wp_kses( $input, $allowedposttags);
    return $output;
  }

  /**
   * Sanitize editor data. Much like textarea remove <script> and <html>.
   * However, if the user can create unfiltered HTML allow it.
   *
   * @param $input
   *
   * @return string
   */
  static function editor($input) {
    if ( current_user_can( 'unfiltered_html' ) ) {
      $output = $input;
    }
    else {
      global $allowedtags;
      $output = wpautop(wp_kses( $input, $allowedtags));
    }
    return $output;
  }

  /**
   * Sanitize Hex Color Value
   *
   * If the hex does not validate return a default instead.
   *
   * @param $hex
   * @param string $default
   *
   * @return string
   */
  static function hex( $hex, $default = '#000000' ) {
    if ( acpt_validate::hex( $hex ) ) {
      return $hex;
    }
    return $default;
  }

}