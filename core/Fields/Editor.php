<?php
namespace TypeRocket\Fields;

use \TypeRocket\Sanitize as Sanitize;

class Editor extends Field
{

    public $options = array();

    function __construct()
    {
        wp_enqueue_media();
        $this->repeatable = false;
    }

    function render()
    {
        $value    = Sanitize::editor( $this->getValue() );
        $settings = $this->options;

        $override = array(
            'textarea_name' => $this->getAttribute('name')
        );

        $defaults = array(
            'textarea_rows' => 10,
            'teeny'         => true,
            'tinymce'       => array( 'plugins' => 'wordpress' )
        );

        $settings = array_merge( $defaults, $settings, $override );

        ob_start();
        wp_editor( $value, 'wp_editor_' . $this->getName(), $settings );
        $html = ob_get_clean();

        return $html;
    }

}