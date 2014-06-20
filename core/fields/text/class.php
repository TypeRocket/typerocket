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

    unset($this->attr['name']);
    return tr_html::input($this->type, $name, $value, $this->attr);
  }

}