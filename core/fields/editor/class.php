<?php
class tr_field_editor extends tr_field {

  function __construct() {
    wp_enqueue_media();
    $this->repeatable = false;
  }

  function render() {
    $value = tr_sanitize::editor($this->get_value());
    $settings = $this->options;

    $override = array(
      'textarea_name' => $this->attr['name']
    );

    $defaults = array(
      'textarea_rows' => 10,
      'teeny' => true,
      'tinymce' => array( 'plugins' => 'wordpress' )
    );

    $settings = array_merge($defaults, $settings, $override);

    ob_start();
    wp_editor($value, 'wp_editor_' . $this->name, $settings);
    $html = ob_get_clean();
    return $html;
  }

}