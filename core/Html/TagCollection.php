<?php
namespace TypeRocket\Html;

use ArrayObject;

class TagCollection extends ArrayObject
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