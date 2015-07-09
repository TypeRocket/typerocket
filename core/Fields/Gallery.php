<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator as Generator;

class Gallery extends Field
{

    function __construct()
    {
        $paths = \TypeRocket\Config::getPaths();
        wp_enqueue_media();
        wp_enqueue_script( 'typerocket-media', $paths['urls']['assets'] . '/js/media.js', array( 'jquery' ), '1.0',
            true );
    }

    function render()
    {
        $name                = $this->attr['name'];
        $this->attr['class'] = 'image-picker';
        $images              = $this->getValue();
        unset( $this->attr['name'] );
        $generator = new Generator();

        if (empty( $this->settings['button'] )) {
            $this->settings['button'] = 'Insert Images';
        }

        $list = '';

        if (is_array( $images )) {
            foreach ($images as $id) {
                $input = $generator->newInput( 'hidden', $name . '[]', $id )->getString();
                $image = wp_get_attachment_image( $id, 'thumbnail' );

                if ( ! empty( $image )) {
                    $list .= $generator->newElement( 'li', array(
                        'class' => 'image-picker-placeholder'
                    ),
                        '<a href="#remove" class="tr-icon-remove2" title="Remove Image"></a>' . $image . $input )->getString();
                }

            }
        }

        unset( $this->attr['id'] );
        $container = new Generator();
        $html      = $generator->newInput( 'hidden', $name, '0', $this->attr )->getString();

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