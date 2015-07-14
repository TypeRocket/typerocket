<?php

namespace TypeRocket;

/**
 * Class Buffer
 * @package TypeRocket
 */
class Buffer
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
            $index = Sanitize::underscore($index);
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

}