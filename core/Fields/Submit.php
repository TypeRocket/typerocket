<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html as Html;

class Submit extends Field
{

    function __construct()
    {
        $this->setType('submit');
        $this->debuggable = false;
    }

    function render()
    {
        $name = '_tr_submit_form';

        $this->removeAttribute('id');
        $this->setAttribute('id', $name);

        $value = esc_attr( $this->getAttribute('value') );
        $this->removeAttribute('value');
        $this->removeAttribute('name');
        $class = $this->getAttribute('class');


        if (isset( $attr )) {
            $class .= ' button button-primary';
        } else {
            $class = ' button button-primary';
        }

        $this->setAttribute('class', $class);

        $generator = new Html\Generator();
        return $generator->newInput( $this->getType(), $name, $value, $this->getAttributes() )->getString();
    }

}