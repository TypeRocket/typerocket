<?php
namespace TypeRocket\Fields;

use TypeRocket\Html\Generator as Generator,
    TypeRocket\Config as Config,
    TypeRocket\Buffer as Buffer,
    \TypeRocket\Sanitize as Sanitize;

class Matrix extends Field {

    private $mxid = null;
    public $options = null;

    function __construct()
    {

        $paths = Config::getPaths();
        $this->mxid          = md5( microtime( true ) ); // set id for matrix random
        // load everything :(
        wp_enqueue_script( 'typerocket-booyah', $paths['urls']['assets'] . '/js/booyah.js', array( 'jquery' ),
            '1.0', true );
        wp_enqueue_script( 'jquery-ui-sortable', array( 'jquery' ), '1.0', true );

        // date
        wp_enqueue_style( 'tr-date-picker', $paths['urls']['assets'] . '/css/date-picker.css' );
        wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ), '1.0', true );

        // color
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );

        // images and gallery
        wp_enqueue_media();
        wp_enqueue_script( 'typerocket-media', $paths['urls']['assets'] . '/js/media.js', array( 'jquery' ), '1.0',
            true );
        wp_enqueue_script( 'typerocket-items', $paths['urls']['assets'] . '/js/items.js',
            array( 'jquery' ), '1.0', true );

        // load just matrix :)
        wp_enqueue_script( 'tr-matrix-core', $paths['urls']['assets'] . '/js/matrix.js', array( 'jquery' ), '1.0', true );
    }

    function getString()
    {

        // setup select list of files
        $select = $this->get_select_html();
        $name = $this->getName();
        $blocks = $this->getMatrixBlocks();
        $settings = $this->getSettings();

        // add controls
        if (isset( $settings['help'] )) {
            $help = "<div class=\"help\"> <p>{$settings['help']}</p> </div>";
            $this->removeSetting('help');
        } else {
            $help = '';
        }

        $generator = new Generator();
        $default_null = $generator->newInput('hidden', $this->getAttribute('name'), null)->getString();

        // add it all
        $html = "
<div class='tr-matrix control-section tr-repeater'>
<div class='matrix-controls controls'>
{$select}
<div class=\"tr-repeater-button-add\">
<input type=\"button\" value=\"Add New\" data-id='{$this->mxid}' data-folder='{$name}' class=\"button matrix-button\">
</div>
<div class=\"button-group\">
<input type=\"button\" value=\"Flip\" class=\"flip button\">
<input type=\"button\" value=\"Contract\" class=\"tr_action_collapse button\">
<input type=\"button\" value=\"Clear All\" class=\"clear button\">
</div>
{$help}
</div>
<div>{$default_null}</div>
<div id=\"{$this->mxid}\" class='matrix-fields tr-repeater-fields ui-sortable'>{$blocks}</div></div>";

        return $html;
    }

    private function clean_file_name( $name )
    {

        $name = Sanitize::underscore($name);
        $name = str_replace( '-', ' ', $name );

        return ucwords( $name );
    }

    private function get_select_html()
    {
        $paths = Config::getPaths();
        $name = $this->getName();
        $dir = $paths['matrix'] . '/' . $name;

        if (file_exists( $dir )) {

            $files = preg_grep( '/^([^.])/', scandir( $dir ) );

            $select = "<select data-mxid=\"{$this->mxid}\" class=\"matrix-select-{$name}\">";

            foreach ($files as $f) {
                if (file_exists( $dir . '/' . $f )) {
                    $path = pathinfo( $f );
                    $generator = new Generator();

                    $attr = array( 'value'      => $path['filename'],
                                   'data-file'  => $path['filename'],
                                   'data-group' => $this->getForm()->getGroup()
                    );
                    $select .= $generator->newElement( 'option', $attr, $this->clean_file_name( $path['filename'] ) )->getString();
                }
            }

            $select .= '</select>';

        } else {
            $select = "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> Add a folder for Matrix <code>{$dir}</code> and add your matrix files to it.</div>";
        }

        return $select;

    }

    private function getMatrixBlocks()
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

                    $form->setCurrentField(null);
                    $tr_matrix_group = $this->getName();
                    $tr_matrix_type  = lcfirst( $tr_matrix_type );
                    $root_group        = $form->getGroup();
                    $form->setDebugStatus(false);

                    $form->setGroup($root_group . "[{$tr_matrix_group}][{$tr_matrix_key}][{$tr_matrix_type}]");
                    $file        = $paths['matrix'] . "/" . $this->getName() . "/{$tr_matrix_type}.php";
                    $classes = "matrix-field-group tr-repeater-group matrix-type-{$tr_matrix_type} matrix-group-{$tr_matrix_group}";
                    $remove = '#remove';
                    ?>
                    <div class="<?php echo $classes; ?>">
                        <div class="repeater-controls">
                            <div class="collapse"></div>
                            <div class="move"></div>
                            <a href="<?php echo $remove; ?>" class="remove" title="remove"></a>
                        </div>
                        <div class="repeater-inputs">
                            <?php
                            if (file_exists( $file )) {
                                /** @noinspection PhpIncludeInspection */
                                include( $file );
                            } else {
                                echo "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> No Matrix file found <code>{$file}</code></div>";
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