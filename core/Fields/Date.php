<?php
namespace TypeRocket\Fields;

class Date extends Field {

  function __construct() {
    wp_enqueue_style( 'tr-date-picker', tr::$paths['urls']['assets'] . '/css/date-picker.css' );
    wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ), '1.0', true );
    $this->type = 'text';
  }

  function render() {
    $name = $this->attr['name'];
    $value = esc_attr($this->get_value());

    if(isset($this->attr['class'])) {
      $this->attr['class'] .= ' date-picker';
    } else {
      $this->attr['class'] = ' date-picker';
    }

    unset($this->attr['name']);
    return tr_html::input($this->type, $name, $value, $this->attr);
  }

}