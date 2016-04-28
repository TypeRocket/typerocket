<?php

namespace TypeRocket\Fields;

use TypeRocket\Buffer;
use TypeRocket\Config;
use TypeRocket\Html\Generator;

class Builder extends Matrix
{

    public function enqueueScripts() {
        $paths = Config::getPaths();
        $assets = $paths['urls']['assets'];
        wp_enqueue_script( 'jquery-ui-sortable', [ 'jquery' ], '1.0', true );
        wp_enqueue_script( 'jquery-ui-datepicker', [ 'jquery' ], '1.0', true );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_media();
        wp_enqueue_script( 'typerocket-editor', $assets . '/js/redactor.min.js', ['jquery'], '1.0', true );
    }

    /**
     * Run on construction
     */
    protected function init()
    {
        $this->mxid = md5( microtime( true ) );
        $this->setType( 'builder' );
    }

    public function getString()
    {
        $buffer = new Buffer();
        $buffer->startBuffer();
        ?>

        <div class="tr-builder">

            <div class="controls">
                <div class="select">
                    <?php echo $this->getSelectHtml(); ?>
                </div>
                <ul class="tr-components">
                    <li>
                        <p>Builder</p>
                    </li>
                </ul>
            </div>

            <div class="tr-frame-fields" id="<?php echo $this->mxid; ?>">
                <?php echo $this->getBuilderBlocks(); ?>
            </div>

        </div>

        <?php
        $buffer->indexBuffer('main');
        return $buffer->getBuffer('main');
    }

    private function getSelectHtml()
    {

        $name = $this->getName();
        $options = $this->getOptions();
        $options = $options ? $options : $this->setOptionsFromFolder()->getOptions();

        if ($options) {
            $generator = new Generator();
            $generator->newElement( 'ul', array(
                'data-mxid' => $this->mxid,
                'class' => "matrix-select-{$name}",
                'data-group' => $this->getForm()->getGroup()
            ) );

            foreach ($options as $name => $value) {

                $attr['data-value'] = $value;
                $attr['class'] = 'builder-select-option';
                $attr['data-id'] = $this->mxid;
                $attr['data-folder'] = $this->getName();

                $generator->appendInside( 'li', $attr, $name );
            }

            $select = $generator->getString();

        } else {

            $paths = Config::getPaths();
            $dir = $paths['components'] . '/' . $name;

            $select = "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> Add a files for Matrix <code>{$dir}</code> and add your matrix files to it.</div>";
        }

        return $select;

    }

    private function getBuilderBlocks()
    {

        $val = $this->getValue();
        $utility = new Buffer();
        $blocks = '';
        $form = $this->getForm();
        $paths = Config::getPaths();

        if (is_array( $val )) {

            $utility->startBuffer();

            foreach ($val as $tr_matrix_key => $data) {
                foreach ($data as $tr_matrix_type => $fields) {

                    $tr_matrix_group = $this->getName();
                    $tr_matrix_type  = lcfirst( $tr_matrix_type );
                    $root_group        = $form->getGroup();
                    $form->setDebugStatus(false);

                    if($root_group) {
                        $root_group .= '.';
                    }

                    $form->setGroup($root_group . "{$tr_matrix_group}.{$tr_matrix_key}.{$tr_matrix_type}");
                    $file        = $paths['components'] . "/" . $this->getName() . "/{$tr_matrix_type}.php";
                    $classes = "builder-field-group builder-type-{$tr_matrix_type} builder-group-{$tr_matrix_group}";
                    ?>
                    <div class="<?php echo $classes; ?>">
                        <div class="builder-inputs">
                            <?php
                            if (file_exists( $file )) {
                                /** @noinspection PhpIncludeInspection */
                                include( $file );
                            } else {
                                echo "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> No Builder file found <code>{$file}</code></div>";
                            }
                            ?>
                        </div>
                    </div>
                    <?php

                    $form->setGroup($root_group);
                    $form->setCurrentField($this);

                }
            }

            $utility->indexBuffer('fields');

            $blocks = $utility->getBuffer('fields');
            $utility->cleanBuffer();

        }

        return trim($blocks);

    }

}