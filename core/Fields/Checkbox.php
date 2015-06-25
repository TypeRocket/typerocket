<?php
namespace TypeRocket\Fields;

class Checkbox extends Field {

  function render() {
    $name = $this->attr['name'];
    $this->type = 'checkbox';
    $option = esc_attr($this->get_value());
    unset($this->attr['name']);

    if($option == '1') {
      $this->attr['checked'] = 'checked';
    }

    $field = "<label>";
    if($this->settings['default'] !== false) {
      $field .= tr_html::input('hidden', $name, '0', $this->attr);
    }
    $field .= tr_html::input($this->type, $name, '1', $this->attr);
    $field .= "<span>{$this->settings['text']}</span></label>";

    return $field;
  }

}