<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator;

class Image extends Field implements ScriptField
{
    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'image' );
    }

    public function enqueueScripts() {
        wp_enqueue_media();
    }

    /**
     * Covert Image to HTML string
     */
    public function getString()
    {
        $name = $this->getNameAttributeString();
        $this->setAttribute( 'class', 'image-picker' );
        $value = esc_attr( $this->getValue() );

        $this->removeAttribute( 'name' );
        $generator = new Generator();

        if ( ! $this->getSetting( 'button' )) {
            $this->setSetting( 'button', 'Insert Image' );
        }

        if ($value != "") {
            $image = wp_get_attachment_image( (int) $value, 'thumbnail' );
        } else {
            $image = '';
        }

        if (empty( $image )) {
            $value = '';
        }

        $html = $generator->newInput( 'hidden', $name, $value, $this->getAttributes() )->getString();
        $html .= '<div class="button-group">';
        $html .= $generator->newElement( 'input', [
            'type'  => 'button',
            'class' => 'image-picker-button button',
            'value' => $this->getSetting( 'button' )
        ])->getString();
        $html .= $generator->newElement( 'input', [
            'type'  => 'button',
            'class' => 'image-picker-clear button',
            'value' => 'Clear'
        ])->getString();
        $html .= '</div>';
        $html .= $generator->newElement( 'div', [
            'class' => 'image-picker-placeholder'
        ], $image )->getString();

        return $html;
    }

}