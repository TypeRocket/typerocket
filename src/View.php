<?php

namespace TypeRocket;

class View
{
    static public $data = [];
    static public $file = null;

    static public function page($resource, $action = null, $item_id = null)
    {
        $query = [];
        $query['page'] = $resource . '_' . $action;

        if($item_id) {
            $query['item_id'] = (int) $item_id;
        }

        return admin_url() . 'admin.php?' . http_build_query($query);
    }

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