<?php
class tr_field_color extends tr_field {

  function __construct() {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
    $this->repeatable = false;
  }

  function render() {
    $name = $this->attr['name'];
    $value = esc_attr($this->get_value());
    unset($this->attr['name']);

    if(isset($this->attr['class'])) {
      $this->attr['class'] .= ' color-picker';
    } else {
      $this->attr['class'] = 'color-picker';
    }

    wp_localize_script('typerocket-scripts', $this->prefix.'_'.$this->name.'_color_palette', $this->settings['palette'] );

    if(isset($this->settings['default'])) {
      $this->attr['data-default-color'] = $this->settings['default'];
    }

    return tr_html::input($this->type, $name, $value, $this->attr);
  }

}