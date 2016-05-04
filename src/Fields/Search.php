<?php

namespace TypeRocket\Fields;

use TypeRocket\Html\Generator;

class Search extends Field
{

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
        $title = 'No selection... Search and click on a result';
        $type = $this->getSetting('post_type', 'any');
        $taxonomy = $this->getSetting('taxonomy', '');

        $search_attributes = [
            'placeholder' => 'Type to search...',
            'class' => 'tr-link-search-input'
        ];

        if($value < 1) {
            $value = null;
        } elseif( empty($taxonomy) ) {
            $search_attributes['data-posttype'] = $type;
            $title = 'Selection: <b>' . get_post_field('post_title', $value) . '</b>';
        } else {
            $search_attributes['data-taxonomy'] = $taxonomy;
            $term = get_term( $value, $taxonomy );
            $title = 'Selection: <b>' . $term->term_name . '</b>';
        }

        $field = $input->newInput($this->getType(), null, null,  $search_attributes)->getString();
        $field .= $input->newInput( 'hidden', $name, $value, $this->getAttributes() )->getString();
        $field .= '<div class="tr-link-search-page">'.$title.'</div>';
        $field .= '<ul class="tr-link-search-results"></ul>';

        return $field;
    }
}