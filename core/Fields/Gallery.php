<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator as Generator,
    \TypeRocket\Config as Config;

class Gallery extends Field
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
        $images              = $this->getValue();
        $this->removeAttribute('name');
        $generator = new Generator();

        if (empty( $this->settings['button'] )) {
            $this->settings['button'] = 'Insert Images';
        }

        $list = '';

        if (is_array( $images )) {
            foreach ($images as $id) {
                $input = $generator->newInput( 'hidden', $name . '[]', $id )->getString();
                $image = wp_get_attachment_image( $id, 'thumbnail' );
                $remove = '#remove';

                if ( ! empty( $image )) {
                    $list .= $generator->newElement( 'li', array(
                        'class' => 'image-picker-placeholder'
                    ),
                        '<a class="tr-icon-remove2"  title="Remove Image" href="'.$remove.'"></a>' . $image . $input )->getString();
                }

            }
        }

        $this->removeAttribute('id');
        $container = new Generator();
        $html      = $generator->newInput( 'hidden', $name, '0', $this->getAttributes() )->getString();

        $button = $generator->newElement( 'input', array(
            'type'  => 'button',
            'class' => 'gallery-picker-button button',
            'value' => $this->settings['button']
        ) )->getTag();

        $clear = $generator->newElement( 'input', array(
            'type'  => 'button',
            'class' => 'gallery-picker-clear button',
            'value' => 'Clear'
        ) )->getTag();

        $html .= $container->newElement( 'div',
            array( 'class' => 'button-group' ) )->appendInside( $button )->appendInside( $clear )->getString();

        $html .= $generator->newElement( 'ul', array(
            'class' => 'tr-gallery-list cf'
        ), $list )->getString();

        return $html;
    }

}