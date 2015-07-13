<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator as Generator,
    \TypeRocket\Sanitize as Sanitize;

class Textarea extends Field
{

    function getString()
    {
        $max = '';
        $generator = new Generator();
        $value = $this->getValue();

        if ($this->getSetting('sanitize') != 'raw') {
            $value = Sanitize::textarea( $value );
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