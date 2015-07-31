<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html;

class Radio extends Field implements OptionField
{

    private $options = array();

    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'radio' );
    }

    /**
     * Covert Radio to HTML string
     */
    public function getString()
    {
        $name       = $this->getNameAttributeString();
        $default = $this->getSetting('default');
        $option = $this->getValue();
        $option     = ! is_null($option) ? $this->getValue() : $default;
        $this->removeAttribute('name');
        $this->removeAttribute('id');
        $generator = new Html\Generator();

        $field = '<ul class="data-full">';

        foreach ($this->options as $key => $value) {
            if ($option === $value) {
                $this->setAttribute('checked', 'checked');
            } else {
                $this->removeAttribute('checked');
            }

            $field .= "<li><label>";
            $field .= $generator->newInput( 'radio', $name, $value, $this->getAttributes() )->getString();
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