<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html as Html;

class Image extends Field {

  function __construct() {
	  $paths = \TypeRocket\Config::getPaths();
    wp_enqueue_media();
    wp_enqueue_script( 'typerocket-media', $paths['urls']['assets'] . '/js/media.js', array('jquery'), '1.0', true );
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

    $html = Html::input('hidden', $name, $value, $this->attr);
    $html .= '<div class="button-group">';
    $html .= Html::element('input', array(
      'type' => 'button',
      'class' => 'image-picker-button button',
      'value' => $this->settings['button']
    ));
    $html .= Html::element('input', array(
      'type' => 'button',
      'class' => 'image-picker-clear button',
      'value' => 'Clear'
    ));
    $html .= '</div>';
    $html .= Html::element('div', array(
      'class' => 'image-picker-placeholder'
    ), $image);
    return $html;
  }

}