<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator;

class Gallery extends Field implements ScriptField
{
    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'gallery' );
    }

    public function enqueueScripts() {
        wp_enqueue_media();
    }

    /**
     * Covert Gallery to HTML string
     */
    public function getString()
    {
        $name = $this->getNameAttributeString();
        $this->setAttribute('class', 'image-picker');
        $images = $this->getValue();
        $this->removeAttribute('name');
        $generator = new Generator();

        if (! $this->getSetting( 'button' )) {
            $this->setSetting('button', 'Insert Images');
        }

        $list = '';

        if (is_array( $images )) {
            foreach ($images as $id) {
                $input = $generator->newInput( 'hidden', $name . '[]', $id )->getString();
                $image = wp_get_attachment_image( (int) $id, 'thumbnail' );
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

        $button = $generator->newElement( 'input', [
            'type'  => 'button',
            'class' => 'gallery-picker-button button',
            'value' => $this->getSetting( 'button' )
        ])->getTag();

        $clear = $generator->newElement( 'input', [
            'type'  => 'button',
            'class' => 'gallery-picker-clear button',
            'value' => 'Clear'
        ])->getTag();

        $html .= $container->newElement( 'div',
            ['class' => 'button-group'])->appendInside( $button )->appendInside( $clear )->getString();

        $html .= $generator->newElement( 'ul', [
            'class' => 'tr-gallery-list cf'
        ], $list )->getString();

        return $html;
    }

}