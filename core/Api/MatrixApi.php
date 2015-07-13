<?php
namespace TypeRocket\Api;

class MatrixApi {

    function __construct($group, $type, $form_group, $load = true) {

        if( $load ) {
            $tr_matrix_id = time(); // id for repeater
            $form = tr_form();
            $form->setPopulate(false);
            $form->setDebugStatus(false);

            if(!$form_group) {
                $form_group = '';
            }

            $form->setGroup($form_group . "[{$group}][{$tr_matrix_id}][{$type}]");
            $path = TR_MATRIX_DIR . "/{$group}/{$type}.php";
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
                if(file_exists($path)) {
                    /** @noinspection PhpIncludeInspection */
                    include($path);
                } else {
                    echo "<p>No Matrix File</p>";
                }
                ?>
            </div>
        </div>
        <?php
    }

}