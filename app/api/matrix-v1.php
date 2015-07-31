<?php
// test to see if this is an AJAX call
$tr_loaded = defined('TR_START');
if($tr_loaded) {
    $matrix_group = get_query_var('typerocket_matrix_group', null);
    $matrix_type = get_query_var('typerocket_matrix_type', null);

    $load = apply_filters('tr_matrix_api_load', true, $matrix_group, $matrix_type, $_POST['form_group']);
    if($load) {
        new TypeRocket\Http\Matrix($matrix_group, $matrix_type, $_POST['form_group']);
    }
}

status_header(404);
die();