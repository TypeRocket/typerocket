<?php

namespace TypeRocket;

class View
{
    static public $data = [];
    static public $file = null;

    public function __construct( $file , array $data = [] )
    {
        if( file_exists($file) ) {
            self::$file = $file;
        } else {
            $dots = explode('.', $file);
            self::$file = Config::getPaths()['views'] . '/' . implode('/', $dots) . '.php';
        }

        if( !empty( $data ) ) {
            self::$data = $data;
        }
    }

    public function file() {
        return  self::$file;
    }

    public function data()
    {
        return self::$data;
    }

}