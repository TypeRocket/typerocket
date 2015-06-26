<?php

namespace TypeRocket;

/**
 * Class Utility
 * @package TypeRocket
 */
class Utility
{

    public $buffering = false;
    public $buffer = array();

    function buffer($index = null)
    {

        $this->sanitize_string($index);

        if ($this->buffering === false) {
            if (isset($index) && $index !== '') {
                die('Starting buffer... Index when the buffer ends.');
            }
            ob_start();
            $this->buffering = true;
        } else {
            $this->check($index, 'Ending buffer... add an index.');
            $data = ob_get_clean();
            $this->buffer[$index] = $data;
            $this->buffering = false;
        }

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

    /**
     * Test for value if there is none die.
     *
     * @param $data
     * @param $error
     * @param string $type
     */
	public function check($data, $error, $type = 'string')
    {

    }

}