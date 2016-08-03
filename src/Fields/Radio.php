<?php
namespace TypeRocket\Fields;

use \TypeRocket\Traits\OptionsTrait,
    \TypeRocket\Html;

class Radio extends Field
{

    use OptionsTrait;

    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'radio' );
    }

    /**
     * Covert Radio to HTML string
     */
    public function getString()
    {
        $name    = $this->getNameAttributeString();
        $default = $this->getSetting('default');
        $option  = $this->getValue();
        $option     = ! is_null($option) ? $this->getValue() : $default;
        $this->removeAttribute('name');
        $id = $this->getAttribute('id', '');
        $this->removeAttribute('id');
        $generator = new Html\Generator();

        if($id) {
            $id = " id=\"{$id}\"";
        }

        $field = "<ul class=\"data-full\"{$id}>";

        foreach ($this->options as $key => $value) {
            if ( $option == $value && isset($option) ) {
                $this->setAttribute('checked', 'checked');
            } else {
                $this->removeAttribute('checked');
            }

            $field .= "<li><label>";
            $field .= $generator->newInput( 'radio', $name, $value, $this->getAttributes() )->getString();
            $field .= "<span>{$key}</span></label>";
        }

        $field .= '</ul>';

        return $field;
    }

}