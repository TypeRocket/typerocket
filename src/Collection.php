<?php
namespace TypeRocket;

class Collection extends \ArrayObject
{

    /**
     * Add item to top of collection
     *
     * @param $value
     */
    public function prepend( $value )
    {
        $array = $this->getArrayCopy();
        array_unshift( $array, $value );
        $this->exchangeArray( $array );
    }

    /**
     * Add item to end of collection
     *
     * @param mixed $value
     */
    public function append( $value )
    {
        $this[] = $value;
    }
}
