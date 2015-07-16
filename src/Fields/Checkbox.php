<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator as Generator;

class Checkbox extends Field
{

    public function init()
    {
        $this->setType( 'checkbox' );
    }

    public function getString()
    {
        $name   = $this->getAttribute( 'name' );
        $this->removeAttribute( 'name' );
        $default = $this->getSetting( 'default' );
        $option = $this->getValue();
        $checkbox = new Generator();
        $field = new Generator();

        if ($option == '1' || ! is_null($option) && $option == $this->getAttribute('value')) {
            $this->setAttribute( 'checked', 'checked' );
        } elseif($default === true && is_null($option)) {
            $this->setAttribute( 'checked', 'checked' );
        }

        $checkbox->newInput( 'checkbox', $name, '1', $this->getAttributes() );

        $field->newElement( 'label' )
            ->appendInside( $checkbox )
            ->appendInside( 'span', array(), $this->getSetting( 'text' ) );

        if ($default !== false) {
            $hidden = new Generator();
            $field->prependInside( $hidden->newInput('hidden', $name, '0' ) );
        }

        return $field->getString();
    }

}