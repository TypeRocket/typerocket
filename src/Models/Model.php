<?php
namespace TypeRocket\Models;

abstract class Model {

    private $fillable = array();

    public function setFillable( $fillable, $type = 'meta' )
    {
        $this->fillable[$type] = $fillable;

        return $this;
    }

    function getFillable($type = 'meta')
    {
        return $this->fillable[$type];
    }

}