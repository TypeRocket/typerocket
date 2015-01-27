<?php

class tr_field_text extends tr_field {

  function __construct() {
    $this->type = 'text';
  }

  function render() {
    $name = $this->attr['name'];

    if($this->settings['sanitize'] == 'plain') {
      $value = $this->get_value();
    } else {
      $value = esc_attr($this->get_value());
    }

    if(isset($this->attr['maxlength']) && $this->attr['maxlength'] > 0) {
      $left = (int) $this->attr['maxlength'] - strlen(utf8_decode($value));
      $max = "<p class=\"tr-maxlength\">Characters left: <span>{$left}</span></p>";
    } else {
      $max = '';
    }

    unset($this->attr['name']);
    return tr_html::input($this->type, $name, $value, $this->attr) . $max;
  }

}