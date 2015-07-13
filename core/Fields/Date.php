<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator as Generator;

class Date extends Field
{

    function __construct()
    {
        $paths = \TypeRocket\Config::getPaths();
        wp_enqueue_style( 'tr-date-picker', $paths['urls']['assets'] . '/css/date-picker.css' );
        wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ), '1.0', true );
        $this->setType('text');
    }

    function getString()
    {
        $name  = $this->getAttribute( 'name' );
        $value = esc_attr( $this->getValue() );
        $this->appendStringToAttribute( 'class', ' date-picker' );
        $this->removeAttribute( 'name' );

        $input = new Generator();

        return $input->newInput( $this->getType(), $name, $value, $this->getAttributes() )->getString();
    }

}