<?php
namespace TypeRocket\Fields;

use \TypeRocket\Sanitize as Sanitize;

class WordPressEditor extends Field implements FieldOptions
{

    private $options = array();

    function __construct()
    {
        wp_enqueue_media();
    }

    function getString()
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


    public function setOption( $key, $value )
    {
        $this->options[ $key ] = $value;

        return $this;
    }

    public function setOptions( $options )
    {
        $this->options = $options;

        return $this;
    }

    public function getOption( $key, $default = null )
    {
        if ( ! array_key_exists( $key, $this->options ) ) {
            return $default;
        }

        return $this->options[ $key ];
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function removeOption( $key )
    {
        if ( array_key_exists( $key, $this->options ) ) {
            unset( $this->options[ $key ] );
        }

        return $this;
    }
}