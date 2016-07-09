<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator;
use TypeRocket\Traits\MaxlengthTrait;

class Textarea extends Field
{
    use MaxlengthTrait;

    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'textarea' );
    }

    /**
     * Covert Textarea to HTML string
     */
    public function getString()
    {
        $generator = new Generator();
        $this->setAttribute('name', $this->getNameAttributeString());
        $value = $this->getValue();
        $value = $this->sanitize($value, 'textarea');
        $max = $this->getMaxlength( $value,  $this->getAttribute('maxlength'));

        return $generator->newElement( 'textarea', $this->getAttributes(), $value )->getString() . $max;
    }

}