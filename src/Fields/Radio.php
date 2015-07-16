<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html as Html;

class Radio extends Field
{

    private $options = array();

    public function __construct()
    {
        $this->setType( 'radio' );
    }

    function getString()
    {
        $name       = $this->getAttribute('name');
        $default = $this->getSetting('default');
        $option = $this->getValue();
        $option     = ! is_null($option) ? $this->getValue() : $default;
        $this->removeAttribute('name');
        $this->removeAttribute('id');
        $generator = new Html\Generator();

        $field = '<ul class="data-full">';

        foreach ($this->options as $key => $value) {
            if ($option == $value) {
                $this->setAttribute('checked', 'checked');
            } else {
                $this->removeAttribute('checked');
            }

            $field .= "<li><label>";
            $field .= $generator->newInput( $this->getType(), $name, $value, $this->getAttributes() )->getString();
            $field .= "<span>{$key}</span></label>";
        }

        $field .= '</ul>';

        return $field;
    }

    public function setOption( $key, $value )
    {
        $this->options[ $key ] = $value;

        return $this;
    }

    public function setOptions( $options )
    {
        $this->options = $options;

        return $this;
    }

    public function getOption( $key, $default = null )
    {
        if ( ! array_key_exists( $key, $this->options ) ) {
            return $default;
        }

        return $this->options[ $key ];
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function removeOption( $key )
    {
        if ( array_key_exists( $key, $this->options ) ) {
            unset( $this->options[ $key ] );
        }

        return $this;
    }

}