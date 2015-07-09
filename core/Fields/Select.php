<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator as Generator;

class Select extends Field
{

    function render()
    {
        $this->type = 'radio';
        $option     = esc_attr( $this->getValue() );
        $generator  = new Generator();
        $generator->newElement( 'select', $this->attr );


        foreach ($this->options as $key => $value) {

            $attr['value'] = $value;
            if ($option == $value) {
                $attr['selected'] = 'selected';
            } else {
                unset( $attr['selected'] );
            }

            $generator->appendInside( 'option', $attr, (string) $key );

        }

        return $generator->getString();
    }

}