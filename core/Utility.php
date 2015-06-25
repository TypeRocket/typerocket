<?php
namespace TypeRocket;

class Utility
{

    public $buffering = false;
    public $buffer = [];

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
    protected function unslash(&$v)
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
     */
    protected function merge(&$arg1, $arg2)
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
    protected function sanitize_string(&$name, $spacer = '_')
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
    protected function get_sanitized_string($name, $spacer = '_')
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
    protected function check($data, $error, $type = 'string')
    {

        // TODO: look into using WP_Error https://codex.wordpress.org/Class_Reference/WP_Error
        if ( ! isset($data)) {
            die('TypeRocket Error: ' . $error);
        }

        switch ($type) {
            case 'array' :
                if (isset($data) && ! is_array($data)) {
                    die('TypeRocket Error: ' . $error);
                }
                break;
            case 'bool' :
                if (isset($data) && ! is_bool($data)) {
                    die('TypeRocket Error: ' . $error);
                }
                break;
            default:
                if (isset($data) && ! is_string($data)) {
                    die('TypeRocket Error: ' . $error);
                }
                break;
        }
    }

}