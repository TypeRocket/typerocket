<?php

namespace TypeRocket\Traits;

trait OptionsTrait
{
    protected $options = [];

    public function setOption( $key, $value )
    {
        $this->options[ $key ] = $value;

        return $this;
    }

    public function setOptions( $options, $style = 'standard' )
    {

        switch ($style) {
            case 'flat':
                $options = array_combine($options, $options);
                break;
            case 'flip' :
                $options = array_flip($options);
                break;
        }

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