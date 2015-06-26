<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html as Html;

class Submit extends Field {

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

    return Html::input($this->type, $name, $value, $this->attr);
  }

}