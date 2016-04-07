<?php
namespace TypeRocket\Fields;

use TypeRocket\Fields\Traits\OptionsTrait;
use \TypeRocket\Html;

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
        $name       = $this->getNameAttributeString();
        $default = $this->getSetting('default');
        $option = $this->getValue();
        $option     = ! is_null($option) ? $this->getValue() : $default;
        $this->removeAttribute('name');
        $this->removeAttribute('id');
        $generator = new Html\Generator();

        $field = '<ul class="data-full">';

        foreach ($this->options as $key => $value) {
            if ($option === $value) {
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