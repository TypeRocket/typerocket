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
        $value = (int) $this->getValue();
        $title = 'No page selected... Search and click on a result';

        if($value < 1) {
            $value = null;
        } else {
            $title = get_post_field('post_title', $value);
        }

        $search_attributes = [
            'placeholder' => 'Type to search...',
            'class' => 'tr-link-search-input'
        ];

        $field = $input->newInput($this->getType(), null, null,  $search_attributes)->getString();
        $field .= $input->newInput( 'hidden', $name, $value, $this->getAttributes() )->getString();
        $field .= '<div class="tr-link-search-page">'.$title.'</div>';
        $field .= '<ul class="tr-link-search-results"></ul>';

        return $field;
    }
}