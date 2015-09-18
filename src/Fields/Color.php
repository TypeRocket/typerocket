<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator,
    \TypeRocket\Sanitize;

class Color extends Field implements ScriptField
{
    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'color' );
    }

    public function enqueueScripts() {
        wp_enqueue_script( 'wp-color-picker' );
    }

    /**
     * Covert Color to HTML string
     */
    public function getString()
    {
        $name  = $this->getNameAttributeString();
        $value = Sanitize::hex( $this->getValue() );
        $this->removeAttribute( 'name' );
        $this->appendStringToAttribute( 'class', ' color-picker' );

        wp_localize_script( 'typerocket-scripts', $this->getPrefix() . '_' . $this->getName() . '_color_palette', $this->getSetting( 'palette' ) );

        if ($this->getSetting( 'palette' )) {
            $this->setAttribute( 'data-default-color', $this->getSetting( 'palette' ) );
        }

        $input = new Generator();

        return $input->newInput( 'text', $name, $value, $this->getAttributes() )->getString();
    }

}