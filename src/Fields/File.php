<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html,
    \TypeRocket\Config;

class File extends Field implements ScriptField
{
    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'file' );
    }

    public function enqueueScripts() {
        $paths = Config::getPaths();
        wp_enqueue_media();
        wp_enqueue_script( 'typerocket-media', $paths['urls']['assets'] . '/js/media.js', array( 'jquery' ), '1.0',
            true );
    }

    /**
     * Covert File to HTML string
     */
    function getString()
    {
        // $this->attr['class'] = 'file-picker';
        $name = $this->getNameAttributeString();
        $this->appendStringToAttribute( 'class', ' file-picker' );
        $value = (int) $this->getValue() !== 0 ? $this->getValue() : null;
        $this->removeAttribute( 'name' );
        $generator = new Html\Generator();

        if ( ! $this->getSetting( 'button' )) {
            $this->setSetting( 'button', 'Insert File' );
        }

        if ($value != "") {
            $url  = wp_get_attachment_url( $value );
            $file = '<a target="_blank" href="' . $url . '">' . $url . '</a>';
        } else {
            $file = '';
        }

        $html = $generator->newInput( 'hidden', $name, $value, $this->getAttributes() )->getString();
        $html .= '<div class="button-group">';
        $html .= $generator->newElement( 'input', array(
            'type'  => 'button',
            'class' => 'file-picker-button button',
            'value' => $this->getSetting( 'button' )
        ) )->getString();;
        $html .= $generator->newElement( 'input', array(
            'type'  => 'button',
            'class' => 'file-picker-clear button',
            'value' => 'Clear'
        ) )->getString();;
        $html .= '</div>';
        $html .= $generator->newElement( 'div', array(
            'class' => 'file-picker-placeholder'
        ), $file )->getString();

        return $html;
    }

}