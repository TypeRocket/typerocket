<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator as Generator;

class Text extends Field
{

    function __construct()
    {
        $this->type = 'text';
    }

    function render()
    {
        $max = '';
        $value = $this->getValue();

        if ($this->settings['sanitize'] !== 'raw') {
            $value = esc_attr( $this->getValue() );
        }

        if (isset( $this->attr['maxlength'] ) && $this->attr['maxlength'] > 0) {
            $left = (int) $this->attr['maxlength'] - strlen( utf8_decode( $value ) );
            $max = new Generator();
            $max->newElement('p', array('class' => 'tr-maxlength'), 'Characters left: ')->appendInside('span', array(), $left);
        }

        $this->attr['type'] = $this->type;
        $this->attr['value'] = $value;

        $input = new Generator();

        return $input->newElement('input', $this->attr)->getString() . $max;
    }

}