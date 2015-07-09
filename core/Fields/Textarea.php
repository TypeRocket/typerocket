<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator as Generator,
    \TypeRocket\Sanitize as Sanitize;

class Textarea extends Field
{

    function render()
    {
        $generator = new Generator();

        if ($this->settings['sanitize'] == 'raw') {
            $value = $this->getValue();
        } else {
            $value = Sanitize::textarea( $this->getValue() );
        }

        if (isset( $this->attr['maxlength'] ) && $this->attr['maxlength'] > 0) {
            $left = (int) $this->attr['maxlength'] - strlen( utf8_decode( $value ) );
            $max  = "<p class=\"tr-maxlength\">Characters left: <span>{$left}</span></p>";
        } else {
            $max = '';
        }

        return $generator->newElement( 'textarea', $this->attr, $value )->getString() . $max;
    }

}