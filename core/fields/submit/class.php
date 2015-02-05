<?php
class tr_field_submit extends tr_field {

  function __construct() {
    $this->type = 'submit';
    $this->debuggable = false;
  }

  function render() {
    $name = '_tr_submit_form';

    $value = esc_attr($this->attr['value']);
    unset($this->attr['value']);
    unset($this->attr['name']);

    if(isset($this->attr['class'])) {
      $this->attr['class'] .= ' button button-primary';
    } else {
      $this->attr['class'] = ' button button-primary';
    }

    return tr_html::input($this->type, $name, $value, $this->attr);
  }

}