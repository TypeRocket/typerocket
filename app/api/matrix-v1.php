<?php
// test to see if this is an AJAX call
$tr_loaded = defined('TR_START');
if($tr_loaded) {
    $matrix_group = get_query_var('typerocket_matrix_group', null);
    $matrix_type = get_query_var('typerocket_matrix_type', null);
    $matrix_form = get_query_var('typerocket_matrix_from', null);

    $load = current_user_can('read');
    $load = apply_filters('tr_matrix_api_load', $load);

    if($load) {
        new TypeRocket\Api\MatrixApi($matrix_group, $matrix_type, $_GET['form_group']);
    }
}
unset($tr_loaded);