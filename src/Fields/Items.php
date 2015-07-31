<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator,
    \TypeRocket\Config;

class Items extends Field implements ScriptField
{
    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'items' );
    }

    public function enqueueScripts() {
        $paths = Config::getPaths();
        wp_enqueue_script( 'typerocket-items', $paths['urls']['assets'] . '/js/items.js', array( 'jquery' ),
            '1.0', true );
    }

    /**
     * Covert Items to HTML string
     */
    public function getString()
    {
        $name = $this->getNameAttributeString();
        $this->appendStringToAttribute( 'class', ' items-list' );
        // $this->attr['class'] = 'items-list';
        $items = $this->getValue();
        $this->removeAttribute('name');
        $generator = new Generator();

        if (! $this->getSetting('button') ) {
            $this->setSetting('button', 'Insert Item');
        }

        $list = '';

        if (is_array( $items )) {
            foreach ($items as $value) {

                $sanitize = "\\TypeRocket\\Sanitize::" . $this->getSetting('sanitize', 'attribute');

                if ( is_callable($sanitize)) {
                    $value = call_user_func($sanitize, $value );
                }

                $input = $generator->newInput( 'text', $name . '[]', esc_attr( $value ) )->getString();
                $remove = '#remove';
                $list .= $generator->newElement( 'li', array( 'class' => 'item' ),
                    '<div class="move tr-icon-menu"></div><a href="'.$remove.'" class="tr-icon-remove2 remove" title="Remove Item"></a>' . $input )->getString();

            }
        }

        $this->removeAttribute('id');
        $html = $generator->newInput( 'hidden', $name, '0', $this->getAttributes() )->getString();
        $html .= '<div class="button-group">';
        $html .= $generator->newElement( 'input', array(
            'type'  => 'button',
            'class' => 'items-list-button button',
            'value' => $this->getSetting('button')
        ) )->getString();
        $html .= $generator->newElement( 'input', array(
            'type'  => 'button',
            'class' => 'items-list-clear button',
            'value' => 'Clear'
        ) )->getString();
        $html .= '</div>';

        if (is_null( $name ) && is_string( $this->getAttribute('data-name') )) {
            $name = $this->getAttribute('data-name');
        }

        $html .= $generator->newElement( 'ul', array(
            'data-name' => $name,
            'class'     => 'tr-items-list cf'
        ), $list )->getString();

        return $html;
    }

}
