<?php
// test to see if this is an AJAX call
$tr_loaded = defined('TR_START');
if($tr_loaded) {
    $group = get_query_var('typerocket_matrix_group', null);
    $type = get_query_var('typerocket_matrix_type', null);
    $formGroup = $_POST['form_group'];

    $load = apply_filters('tr_matrix_api_load', true, $group, $type, $formGroup);
    if($load) {

        $tr_matrix_id = time(); // id for repeater
        $form = tr_form();
        $form->setPopulate(false);
        $form->setDebugStatus(false);

        if( ! \TypeRocket\Validate::bracket($formGroup)) {
            $formGroup = '';
        }

        $paths = \TypeRocket\Config::getPaths();

        $form->setGroup($formGroup . "[{$group}][{$tr_matrix_id}][{$type}]");
        $file = $paths['matrix'] . "/{$group}/{$type}.php";

        ?>
        <div class="matrix-field-group tr-repeater-group matrix-type-<?php echo $type; ?> matrix-group-<?php echo $group; ?>">
            <div class="repeater-controls">
                <div class="collapse"></div>
                <div class="move"></div>
                <a href="#remove" class="remove" title="remove"></a>
            </div>
            <div class="repeater-inputs">
                <?php
                if(file_exists($file)) {
                    /** @noinspection PhpIncludeInspection */
                    include($file);
                } else {
                    echo "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> No Matrix file found <code>{$file}</code></div>";
                }
                ?>
            </div>
        </div>
        <?php

        die();

    }
}

status_header(404);
die();