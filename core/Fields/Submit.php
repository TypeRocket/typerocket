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
        $this->appendStringToAttribute('class', ' button button-primary');

        $generator = new Html\Generator();
        return $generator->newInput( $this->getType(), $name, $value, $this->getAttributes() )->getString();
    }

}