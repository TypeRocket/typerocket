<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator;

class Textarea extends Field
{

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
        $max = '';
        $generator = new Generator();
        $this->setAttribute('name', $this->getNameAttributeString());
        $value = $this->getValue();
        $sanitize = "\\TypeRocket\\Sanitize::" . $this->getSetting('sanitize', 'textarea');

        if ( is_callable($sanitize)) {
            $value = call_user_func($sanitize, $value );
        }

        $maxLength = $this->getAttribute('maxlength');

        if ( $maxLength != null && $maxLength > 0) {
            $left = (int) $maxLength - strlen( utf8_decode( $value ) );
            $max = new Generator();
            $max->newElement('p', array('class' => 'tr-maxlength'), 'Characters left: ')->appendInside('span', array(), $left);
            $max = $max->getString();
        }

        return $generator->newElement( 'textarea', $this->getAttributes(), $value )->getString() . $max;
    }

}