<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator as Generator;

class Checkbox extends Field
{

    public function __construct()
    {
        $this->setType( 'checkbox' );
    }

    function render()
    {
        $name   = $this->getAttribute( 'name' );
        $this->removeAttribute( 'name' );
        $option = esc_attr( $this->getValue() );
        $checkbox = new Generator();
        $field = new Generator();

        if ($option == '1' || $option == $this->getAttribute('value')) {
            $this->setAttribute( 'checked', 'checked' );
        }

        $checkbox->newInput( $this->getType(), $name, '1', $this->getAttributes() );

        $field->newElement( 'label' )
            ->appendInside( $checkbox )
            ->appendInside( 'span', array(), $this->getSetting( 'text' ) );

        if ($this->getSetting( 'default' ) !== false) {
            $hidden = new Generator();
            $field->prependInside( $hidden->newInput('hidden', $name, '0' ) );
        }

        return $field->getString();
    }

}