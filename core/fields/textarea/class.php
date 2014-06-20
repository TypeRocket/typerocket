<?php

class tr_field_textarea extends tr_field {

  function render() {
    if($this->settings['sanitize'] == 'plain') {
      $value = $this->get_value();
    } else {
      $value = tr_sanitize::textarea($this->get_value());
    }
    return tr_html::element('textarea', $this->attr, $value);
  }

}