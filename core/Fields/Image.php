<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator as Generator,
    \TypeRocket\Config as Config;

class Image extends Field
{

    function __construct()
    {
        $paths = Config::getPaths();
        wp_enqueue_media();
        wp_enqueue_script( 'typerocket-media', $paths['urls']['assets'] . '/js/media.js', array( 'jquery' ), '1.0',
            true );
    }

    function render()
    {
        $name                = $this->getAttribute('name');
        $this->setAttribute('class', 'image-picker');
        $value               = esc_attr( $this->getValue() );

        $this->removeAttribute('name');
        $generator = new Generator();

        if (empty( $this->settings['button'] )) {
            $this->settings['button'] = 'Insert Image';
        }

        if ($value != "") {
            $image = wp_get_attachment_image( $value, 'thumbnail' );
        } else {
            $image = '';
        }

        if (empty( $image )) {
            $value = '';
        }

        $html = $generator->newInput( 'hidden', $name, $value, $this->getAttributes() )->getString();
        $html .= '<div class="button-group">';
        $html .= $generator->newElement( 'input', array(
            'type'  => 'button',
            'class' => 'image-picker-button button',
            'value' => $this->settings['button']
        ) )->getString();
        $html .= $generator->newElement( 'input', array(
            'type'  => 'button',
            'class' => 'image-picker-clear button',
            'value' => 'Clear'
        ) )->getString();
        $html .= '</div>';
        $html .= $generator->newElement( 'div', array(
            'class' => 'image-picker-placeholder'
        ), $image )->getString();

        return $html;
    }

}