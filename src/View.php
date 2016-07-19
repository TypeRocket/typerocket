<?php

namespace TypeRocket;

class View
{
    static public $data = [];
    static public $file = null;

    static public function file() {
        return  self::$file;
    }

    static public function data()
    {
        return self::$data;
    }

    static public function load( $dots , array $data = [] )
    {
        $dots = explode('.', $dots);

        self::$file = Config::getPaths()['views'] . '/' . implode('/', $dots) . '.php';

        return self::$data = $data;
    }

}