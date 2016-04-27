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
        $input = new Generator();
        $name = $this->getNameAttributeString();
        $value = $this->getValue();
        $sanitize = "\\TypeRocket\\Sanitize::" . $this->getSetting('sanitize', 'raw');

        if ( is_callable($sanitize)) {
            $value = esc_attr( call_user_func($sanitize, $value ) );
        }

        $search_attributes = [
            'placeholder' => 'Type to search...',
            'class' => 'tr-link-search-input'
        ];

        $field = $input->newInput($this->getType(), '', '',  $search_attributes)->getString();
        $field .= $input->newInput( 'hidden', $name, $value, $this->getAttributes() )->getString();
        $field .= '<div class="tr-link-search-page"></div>';
        $field .= '<ul class="tr-link-search-results"></ul>';

        return $field;
    }
}