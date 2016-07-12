<?php

namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator;

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
        }

        if( empty($taxonomy) ) {
            $search_attributes['data-posttype'] = $type;
        } else {
            $search_attributes['data-taxonomy'] = $taxonomy;
        }

        if( empty($taxonomy) && $value ) {
            $post = get_post($value);
            $title = 'Selection: <b>' . $post->post_title . ' (' . $post->post_type . ')</b>';
        } elseif( $value ) {
            $term = get_term( $value, $taxonomy );
            $title = 'Selection: <b>' . $term->name . '</b>';
        }

        $field = $input->newInput($this->getType(), null, null,  $search_attributes)->getString();
        $field .= $input->newInput( 'hidden', $name, $value, $this->getAttributes() )->getString();
        $field .= '<div class="tr-link-search-page">'.$title.'</div>';
        $field .= '<ul class="tr-link-search-results"></ul>';

        return $field;
    }

    /**
     * Search by post type only
     *
     * @param $type
     *
     * @return $this
     */
    public function setPostType($type)
    {
        $this->setSetting('post_type', $type);

        return $this;
    }

    /**
     * Search by taxonomy only
     *
     * @param $taxonomy
     *
     * @return $this
     */
    public function setTaxonomy($taxonomy)
    {
        $this->setSetting('taxonomy', $taxonomy);

        return $this;
    }
}