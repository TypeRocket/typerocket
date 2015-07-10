<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator as Generator;

class Color extends Field
{

    function __construct()
    {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
    }

    function render()
    {
        $name  = $this->getAttribute( 'name' );
        $value = esc_attr( $this->getValue() );
        $this->removeAttribute( 'name' );
        $this->appendStringToAttribute( 'class', ' color-picker' );

        wp_localize_script( 'typerocket-scripts', $this->getPrefix() . '_' . $this->getName() . '_color_palette',
            $this->settings['palette'] );

        if (isset( $this->settings['default'] )) {
            $this->setAttribute( 'data-default-color', $this->settings['default'] );
        }

        $input = new Generator();

        return $input->newInput( $this->getType(), $name, $value, $this->getAttributes() )->getString();
    }

}