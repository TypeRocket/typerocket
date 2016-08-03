<?php

namespace TypeRocket;

class View
{
    static public $data = [];
    static public $file = null;
    static public $template = null;

    /**
     * View constructor.
     *
     * Take a custom file location or dot notation of view location.
     *
     * @param $file
     * @param array $data
     */
    public function __construct( $file , array $data = [] )
    {
        if( file_exists($file) ) {
            self::$file = $file;
            self::$template = $file;
        } else {
            $dots = explode('.', $file);
            self::$file = Config::getPaths()['pages'] . '/' . implode('/', $dots) . '.php';
            self::$template =  Config::getPaths()['views'] . '/' . implode('/', $dots) . '.php';
        }

        if( !empty( $data ) ) {
            self::$data = $data;
        }
    }

    /**
     * Get the file
     *
     * This is used for admin pages
     *
     * @return null|string
     */
    public function file() {
        return self::$file;
    }

    /**
     * Get the template
     *
     * This is used for front-end views
     *
     * @return null|string
     */
    public function template() {
        return self::$template;
    }

    /**
     * Get the data attached to a view.
     *
     * @return array
     */
    public function data()
    {
        return self::$data;
    }

}