<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator;
use \TypeRocket\Traits\MaxlengthTrait;

class Text extends Field
{
    use MaxlengthTrait;

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
        $value = esc_attr( $this->sanitize($value, 'raw') );

        $max = $this->getMaxlength( $value, $this->getAttribute('maxlength'));

        return $input->newInput($this->getType(), $name, $value, $this->getAttributes() )->getString() . $max;
    }

}