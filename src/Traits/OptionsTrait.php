<?php

namespace TypeRocket\Traits;

trait OptionsTrait
{
    protected $options = [];

    /**
     * Set option
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setOption( $key, $value )
    {
        $this->options[ $key ] = $value;

        return $this;
    }

    /**
     * Set all options
     *
     * @param $options
     * @param string $style options include standard, flat, flip
     *
     * @return $this
     */
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

    /**
     * Get option by key
     *
     * @param $key
     * @param null $default
     *
     * @return null
     */
    public function getOption( $key, $default = null )
    {
        if ( ! array_key_exists( $key, $this->options ) ) {
            return $default;
        }

        return $this->options[ $key ];
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Remove option by key
     *
     * @param $key
     *
     * @return $this
     */
    public function removeOption( $key )
    {
        if ( array_key_exists( $key, $this->options ) ) {
            unset( $this->options[ $key ] );
        }

        return $this;
    }
}