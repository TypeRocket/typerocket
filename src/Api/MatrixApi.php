<?php
namespace TypeRocket\Api;

use TypeRocket\Config;
use TypeRocket\Validate;

class MatrixApi {

    function __construct($group, $type, $form_group, $load = true) {

        if( $load ) {
            $tr_matrix_id = time(); // id for repeater
            $form = tr_form();
            $form->setPopulate(false);
            $form->setDebugStatus(false);

            if( ! Validate::bracket($form_group)) {
                $form_group = '';
            }

            $paths = Config::getPaths();

            $form->setGroup($form_group . "[{$group}][{$tr_matrix_id}][{$type}]");
            $file = $paths['matrix'] . "/{$group}/{$type}.php";
        } else {
            http_response_code(404);
            exit();
        }

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

        exit();
    }

}