<?php
// get vars
$tr_resource = get_query_var('typerocket_rest_controller', null);
$tr_item_id = get_query_var('typerocket_rest_item', null);

// set method
$tr_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
$tr_method = (isset($_SERVER['REQUEST_METHOD']) && isset($_POST['_method'])) ? $_POST['_method'] : $tr_method;

// load
$tr_load = current_user_can('read');
$tr_load = apply_filters('tr_rest_api_load', $tr_load);

if($tr_load) {
    $type_rocket_api = new TypeRocket\Api\RestApi();
    $type_rocket_api->init($tr_resource, $tr_item_id, $tr_method, 'v1');
}