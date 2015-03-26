<?php
class tr_field_file extends tr_field {

  function __construct() {
    wp_enqueue_media();
    wp_enqueue_script( 'typerocket-media', tr::$paths['urls']['assets'] . '/js/media.js', array('jquery'), '1.0', true );
  }

  function render() {
    $name = $this->attr['name'];
    $this->attr['class'] = 'file-picker';
    $value = esc_attr($this->get_value());
    unset($this->attr['name']);

    if(empty($this->settings['button'])) {
      $this->settings['button'] = 'Insert File';
    }

    if($value != "") {
      $url = wp_get_attachment_url($value);
      $file = '<a target="_blank" href="'.$url.'">'.$url.'</a>';
    }
    else {
      $file = '';
    }

    $html = tr_html::input('hidden', $name, $value, $this->attr);
    $html .= '<div class="button-group">';
    $html .= tr_html::element('input', array(
      'type' => 'button',
      'class' => 'file-picker-button button',
      'value' => $this->settings['button']
    ));
    $html .= tr_html::element('input', array(
      'type' => 'button',
      'class' => 'file-picker-clear button',
      'value' => 'Clear'
    ));
    $html .= '</div>';
    $html .= tr_html::element('div', array(
      'class' => 'file-picker-placeholder'
    ), $file);
    return $html;
  }

}