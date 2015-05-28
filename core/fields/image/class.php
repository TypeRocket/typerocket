<?php
class tr_field_image extends tr_field {

  function __construct() {
    wp_enqueue_media();
    wp_enqueue_script( 'typerocket-media', tr::$paths['urls']['assets'] . '/js/media.js', array('jquery'), '1.0', true );
  }

  function render() {
    $name = $this->attr['name'];
    $this->attr['class'] = 'image-picker';
    $value = esc_attr($this->get_value());
    unset($this->attr['name']);

    if(empty($this->settings['button'])) {
      $this->settings['button'] = 'Insert Image';
    }

    if($value != "") {
      $image = wp_get_attachment_image($value, 'thumbnail');
    }
    else {
      $image = '';
    }

    if(empty($image)) {
      $value = '';
    }

    $html = tr_html::input('hidden', $name, $value, $this->attr);
    $html .= '<div class="button-group">';
    $html .= tr_html::element('input', array(
      'type' => 'button',
      'class' => 'image-picker-button button',
      'value' => $this->settings['button']
    ));
    $html .= tr_html::element('input', array(
      'type' => 'button',
      'class' => 'image-picker-clear button',
      'value' => 'Clear'
    ));
    $html .= '</div>';
    $html .= tr_html::element('div', array(
      'class' => 'image-picker-placeholder'
    ), $image);
    return $html;
  }

}