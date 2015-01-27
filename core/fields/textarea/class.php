<?php

class tr_field_textarea extends tr_field {

  function render() {
    if($this->settings['sanitize'] == 'plain') {
      $value = $this->get_value();
    } else {
      $value = tr_sanitize::textarea($this->get_value());
    }

    if(isset($this->attr['maxlength']) && $this->attr['maxlength'] > 0) {
      $left = (int) $this->attr['maxlength'] - strlen($value);
      $max = "<p class=\"tr-maxlength\">Characters left: <span>{$left}</span></p>";
    } else {
      $max = '';
    }

    return tr_html::element('textarea', $this->attr, $value) . $max;
  }

}