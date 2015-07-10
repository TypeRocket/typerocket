<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html as Html;

class Radio extends Field
{

    public $options = array();

    public function __construct()
    {
        $this->setType( 'radio' );
    }

    function render()
    {
        $name       = $this->getAttribute('name');
        $default = $this->getSetting('default');
        $option = $this->getValue();
        $option     = ! is_null($option) ? $this->getValue() : $default;
        $this->removeAttribute('name');
        $this->removeAttribute('id');
        $generator = new Html\Generator();

        $field = '<ul class="data-full">';

        foreach ($this->options as $key => $value) {
            if ($option == $value) {
                $this->setAttribute('checked', 'checked');
            } else {
                $this->removeAttribute('checked');
            }

            $field .= "<li><label>";
            $field .= $generator->newInput( $this->getType(), $name, $value, $this->getAttributes() )->getString();
            $field .= "<span>{$key}</span></label>";
        }

        $field .= '</ul>';

        return $field;
    }

}