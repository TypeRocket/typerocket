<?php
namespace TypeRocket;

class Buffer
{

    private $buffering = false;
    private $buffer = array();

    public function __get( $property )
    {
    }

    public function __set( $property, $value )
    {
    }

    /**
     * Start Buffering output
     *
     * @return $this
     */
    public function startBuffer()
    {
        $this->buffering = true;
        ob_start();

        return $this;

    }

    /**
     * Index Buffered output
     *
     * @param $index
     *
     * @return $this
     */
    public function indexBuffer($index) {

        if($this->buffering) {
            $index = Sanitize::underscore($index);
            $data = ob_get_clean();
            $this->buffer[$index] = $data;
            $this->buffering = false;
        }

        return $this;
    }

    /**
     * Getting Index output by key
     *
     * @param $index
     *
     * @return mixed
     */
    public function getBuffer( $index )
    {
        return $this->buffer[$index];
    }

    /**
     * Remove all data from Buffer
     */
    public function cleanBuffer() {
        $this->buffer = array();
    }

}
