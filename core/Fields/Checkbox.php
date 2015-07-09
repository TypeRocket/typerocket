<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html as Html;

class Checkbox extends Field {

  function render() {
    $name = $this->attr['name'];
    $this->type = 'checkbox';
    $option = esc_attr($this->getValue());
    unset($this->attr['name']);

    if($option == '1') {
      $this->attr['checked'] = 'checked';
    }

    $field = "<label>";
    if($this->settings['default'] !== false) {
      $field .= Html::input('hidden', $name, '0', $this->attr);
    }
    $field .= Html::input($this->type, $name, '1', $this->attr);
    $field .= "<span>{$this->settings['text']}</span></label>";

    return $field;
  }

}