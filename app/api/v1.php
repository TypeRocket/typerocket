<?php
$resource = get_query_var('typerocket_controller', null);
$id = get_query_var('typerocket_item', null);

// set method
$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
$method = (isset($_SERVER['REQUEST_METHOD']) && isset($_POST['_method'])) ? $_POST['_method'] : $method;

if (isset($_POST['_method'])) {
    unset($_POST['_method']);
}

$type_rocket_api = new TypeRocket\Api();
$type_rocket_api->init($resource, $id, $method, 'v1');
unset($type_rocket_api);


