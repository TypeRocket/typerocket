<?php
// test to see if this is an AJAX call
$tr_load_ok = defined('TR_START');
if($tr_load_ok) :

    $matrix_group = get_query_var('typerocket_matrix_group', null);
    $matrix_type = get_query_var('typerocket_matrix_type', null);
    $matrix_form = get_query_var('typerocket_matrix_from', null);

    class MatrixApi {

        function __construct($group, $type, $form) {

            if( function_exists('current_user_can') && current_user_can('read') ) {
                $tr_matrix_id = time();
                $tr_matrix_group = $group;
                $tr_matrix_type = $type;
                $tr_matrix_form_group = $form;

                $form = tr_form();
                $form->setPopulate(false);

                if(!$tr_matrix_form_group) {
                    $tr_matrix_form_group = '';
                }

                $form->setGroup($tr_matrix_form_group . "[{$tr_matrix_group}][{$tr_matrix_id}][{$tr_matrix_type}]");
                $path = TR_MATRIX_DIR . "/{$tr_matrix_group}/{$tr_matrix_type}.php";
            } else {
                http_response_code(404);
                exit();
            }

            ?>
            <div class="matrix-field-group tr-repeater-group matrix-type-<?php echo $tr_matrix_type; ?> matrix-group-<?php echo $tr_matrix_group; ?>">
                <div class="repeater-controls">
                    <div class="collapse"></div>
                    <div class="move"></div>
                    <a href="#remove" class="remove" title="remove"></a>
                </div>
                <div class="repeater-inputs">
                <?php include($path); ?>
                </div>
            </div>
            <?php
        }

    }

    new MatrixApi($matrix_group, $matrix_type, $_GET['form_group']);

endif;