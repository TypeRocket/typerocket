<?php

namespace TypeRocket;

class View
{
    static public $data = [];
    static public $file = null;

    public function __construct( $dots , array $data = [] )
    {
        $dots = explode('.', $dots);

        self::$file = Config::getPaths()['views'] . '/' . implode('/', $dots) . '.php';
        self::$data = $data;
    }

    public function file() {
        return  self::$file;
    }

    public function data()
    {
        return self::$data;
    }

}