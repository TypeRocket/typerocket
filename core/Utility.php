<?php

namespace TypeRocket;

/**
 * Class Utility
 * @package TypeRocket
 */
class Utility
{

    private $buffering = false;
    private $buffer = array();

    public function startBuffer()
    {
        $this->buffering = true;
        ob_start();

        return $this;

    }

    public function indexBuffer($index) {

        if($this->buffering) {
            $this->sanitize_string($index);
            $data = ob_get_clean();
            $this->buffer[$index] = $data;
            $this->buffering = false;
        }

        return $this;
    }

    public function getBuffer( $index )
    {
        return $this->buffer[$index];
    }

    public function cleanBuffer() {
        $this->buffer = array();
    }

    /**
     * Remove slashes from a string
     *
     * @param $v
     */
    public function unslash(&$v)
    {
        if (is_string($v)) {
            $v = wp_unslash($v);
        } elseif (is_array($v)) {
            $v = stripslashes_deep($v);
        }
    }

    /**
     * Merge array
     *
     *  Set first value to the new value
     *  @param $arg1
     *  @param $arg2
     *
     */
	public function merge(&$arg1, $arg2)
    {
        if (is_array($arg1) && is_array($arg2)) {
            $arg1 = array_merge($arg1, $arg2);
        }
    }

    /**
     * Replace white space with underscore and make all text lowercase
     *
     * @param $name
     * @param $spacer
     *
     * @return mixed
     */
	public function sanitize_string(&$name, $spacer = '_')
    {
        if (is_string($name)) {
            $name = strtolower(trim(sanitize_title($name, '')));
            $pattern = '/(\-+)/';
            $replacement = $spacer;
            $name = preg_replace($pattern, $replacement, $name);
        }
    }

    /**
     * Replace white space with underscore and make all text lowercase
     *
     * @param $name
     * @param $spacer
     *
     * @return mixed
     */
	public function get_sanitized_string($name, $spacer = '_')
    {
        $return = array();

        if (is_string($name)) {
            $name = strtolower(trim(sanitize_title($name, '')));
            $pattern = '/(\-+)/';
            $replacement = $spacer;

            $return = preg_replace($pattern, $replacement, $name);
        }

        return $return;
    }

}