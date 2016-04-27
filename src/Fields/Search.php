<?php

namespace TypeRocket\Fields;

use TypeRocket\Html\Generator;

class Search extends Field implements ScriptField
{

    public function enqueueScripts()
    {
        wp_enqueue_script( 'wp-link' );
    }

    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'text' );
    }

    /**
     * Covert Test to HTML string
     */
    public function getString()
    {
        $max = '';
        $input = new Generator();
        $name = $this->getNameAttributeString();
        $value = $this->getValue();
        $sanitize = "\\TypeRocket\\Sanitize::" . $this->getSetting('sanitize', 'raw');

        if ( is_callable($sanitize)) {
            $value = esc_attr( call_user_func($sanitize, $value ) );
        }

        $this->appendStringToAttribute('class', 'tr-link', ' ');

        return $input->newInput($this->getType(), $name, $value, $this->getAttributes() )->getString() . $max;
    }
}