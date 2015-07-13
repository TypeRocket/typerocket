<?php
namespace TypeRocket;

class Collection extends \ArrayObject
{
    public function prepend($value) {
        $array = $this->getArrayCopy();
        array_unshift($array, $value);
        $this->exchangeArray($array);
    }

    public function append($value) {
        $this[] = $value;
    }
}
