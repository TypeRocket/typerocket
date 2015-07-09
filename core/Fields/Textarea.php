<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html as Html;
use \TypeRocket\Sanitize as Sanitize;

class Textarea extends Field
{

    function render()
    {
        if ($this->settings['sanitize'] == 'plain') {
            $value = $this->get_value();
        } else {
            $value = Sanitize::textarea( $this->get_value() );
        }

        if (isset( $this->attr['maxlength'] ) && $this->attr['maxlength'] > 0) {
            $left = (int) $this->attr['maxlength'] - strlen( utf8_decode( $value ) );
            $max  = "<p class=\"tr-maxlength\">Characters left: <span>{$left}</span></p>";
        } else {
            $max = '';
        }

        return Html::element( 'textarea', $this->attr, $value ) . $max;
    }

}