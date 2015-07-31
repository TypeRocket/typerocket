<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator,
    \TypeRocket\Config;

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
        $paths = Config::getPaths();
        wp_enqueue_media();
        wp_enqueue_script( 'typerocket-media', $paths['urls']['assets'] . '/js/media.js', array( 'jquery' ), '1.0',
            true );
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
        $html .= $generator->newElement( 'input', array(
            'type'  => 'button',
            'class' => 'image-picker-button button',
            'value' => $this->getSetting( 'button' )
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