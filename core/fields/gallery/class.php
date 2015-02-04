<?php
class tr_field_gallery extends tr_field {

  function __construct() {
    wp_enqueue_media();
    wp_enqueue_script( 'typerocket-media', tr::$paths['urls']['assets'] . '/js/media.js', array('jquery'), '1.0', true );
  }

  function render() {
    $name = $this->attr['name'];
    $this->attr['class'] = 'image-picker';
    $images = $this->get_value();
    unset($this->attr['name']);

    if(empty($this->settings['button'])) {
      $this->settings['button'] = 'Insert Images';
    }

    $list = '';

    if(is_array($images)) {
      foreach($images as $id) {
        $input = tr_html::input('hidden', $name . '[]', $id);
        $image = wp_get_attachment_image($id, 'thumbnail');

        $list .= tr_html::element('li', array(
          'class' => 'image-picker-placeholder'
        ), '<a href="#remove" class="tr-icon-remove2" title="Remove Image"></a>' . $image . $input );

      }
    }

    unset($this->attr['id']);
    $html = tr_html::input('hidden', $name, '0', $this->attr);
    $html .= '<div class="button-group">';
    $html .= tr_html::element('input', array(
      'type' => 'button',
      'class' => 'gallery-picker-button button',
      'value' => $this->settings['button']
    ));
    $html .= tr_html::element('input', array(
      'type' => 'button',
      'class' => 'gallery-picker-clear button',
      'value' => 'Clear'
    ));
    $html .= '</div>';

    $html .= tr_html::element('ul', array(
      'class' => 'tr-gallery-list cf'
    ), $list);
    return $html;
  }

}