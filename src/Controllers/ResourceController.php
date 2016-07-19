<?php

namespace TypeRocket\Controllers;

abstract class ResourceController extends Controller
{
    abstract function edit( $id );

    abstract function add();

    abstract function read( $id );

    abstract function index();

    abstract function delete($id);

    static public function page($resource, $action = null, $item_id = null)
    {
        $query = [];
        $query['page'] = $resource . '_' . $action;

        if($item_id) {
            $query['item_id'] = (int) $item_id;
        }

        return admin_url() . 'admin.php?' . http_build_query($query);
    }

}