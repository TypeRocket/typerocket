<?php

class tr_field_select extends tr_field {

  function render() {
    $this->type = 'radio';
    $option = esc_attr($this->get_value());
    $field = tr_html::open_element('select', $this->attr);
    foreach($this->options as $key => $value) {

      $attr['value'] = $value;
      if($option == $value) {
        $attr['selected'] = 'selected';
      }
      else {
        unset($attr['selected']);
      }

      $field .= tr_html::element('option', $attr, $key);

    }
    $field .= '</select>';
    return $field;
  }

}