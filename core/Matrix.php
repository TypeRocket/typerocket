<?php
namespace TypeRocket;

use TypeRocket\Html\Generator as Generator;

class Matrix {

    private $name = null;
    /** @var Form */
    private $form = null;
    private $settings = array();
    private $include_files = array();
    private $label = null;

    function __construct( $name, Form $form, array $settings = array(), $label = false, array $include_files = array() )
    {

        $paths = Config::getPaths();
        $this->name          = (string) $name;
        $this->form          = $form;
        $this->include_files = $include_files;
        $this->settings      = $settings;
        $this->label         = $label;
        $this->mxid          = md5( microtime( true ) ); // set id for matrix random
        // load everything :(
        wp_enqueue_media();
        wp_enqueue_script( 'typerocket-booyah', $paths['urls']['assets'] . '/js/booyah.js', array( 'jquery' ),
            '1.0', true );
        wp_enqueue_script( 'jquery-ui-sortable', array( 'jquery' ), '1.0', true );
        wp_enqueue_style( 'tr-date-picker', $paths['urls']['assets'] . '/css/date-picker.css' );
        wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ), '1.0', true );
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'typerocket-media', $paths['urls']['assets'] . '/js/media.js', array( 'jquery' ), '1.0',
            true );
        wp_enqueue_script( 'typerocket-items-list', $paths['urls']['assets'] . '/js/items-list.js',
            array( 'jquery' ), '1.0', true );
        wp_enqueue_script( 'jquery-ui-slider', array( 'jquery' ) );
        wp_enqueue_style( 'tr-time-picker-style', $paths['urls']['assets'] . '/css/time-picker.css' );
        wp_enqueue_script( 'tr-time-picker-script', $paths['urls']['assets'] . '/js/time-picker.js',
            array( 'jquery', 'jquery-ui-slider' ), '1.0', true );

        // load just matrix :)
        wp_enqueue_script( 'tr_matrix', $paths['urls']['assets'] . '/js/matrix.js', array( 'jquery' ), true );
    }

    function add()
    {

        // setup select list of files
        $select = $this->get_select_html();
        $debug  = $this->get_debug_html();
        $help   = $this->get_help_html();
        $label  = $this->get_label_html();
        $group = $this->form->getGroup();

        // add it all
        echo "
<div class='tr-matrix control-section tr-repeater'>
{$debug}
<div class='matrix-controls controls'>
{$label}
{$select}
<div class=\"tr-repeater-button-add\">
<input type=\"button\" value=\"Add New\" data-id='{$this->mxid}' data-folder='{$this->name}' class=\"button matrix-button\">
</div>
<div class=\"button-group\">
<input type=\"button\" value=\"Flip\" class=\"flip button\">
<input type=\"button\" value=\"Contract\" class=\"tr_action_collapse button\">
<input type=\"button\" value=\"Clear All\" class=\"clear button\">
</div>
{$help}
</div>
<div><input type='hidden' name='tr{$group}[{$this->name}]' /></div>
<div class='matrix-fields matrix-fields-{$this->mxid} tr-repeater-fields ui-sortable'>";
        $this->getMatrixBlocks();
        echo "</div></div>";

        return $this;
    }

    private function clean_file_name( $name )
    {

        $utility = new Utility();
        $name = $utility->get_sanitized_string($name);
        $name = str_replace( '-', ' ', $name );

        return ucwords( $name );
    }

    private function get_help_html()
    {
        if (isset( $this->settings['help'] )) {
            $help =
                "<div class=\"help\">
          <p>{$this->settings['help']}</p>
        </div>";
        } else {
            $help = '';
        }

        return $help;
    }

    private function get_label_html()
    {
        if (is_string( $this->label )) {
            $label = "<div class=\"control-label\"><span class=\"label\">{$this->label}</span></div>";
        } else {
            $label = '';
        }

        return $label;
    }

    private function get_debug_html()
    {
        $debug = '';
        if ( $this->form->getDebugStatus() ) {
            $controller = $this->form->getController();
            $group = $this->form->getGroup();
            $debug =
                "<div class=\"dev\">
        <span class=\"debug\"><i class=\"tr-icon-bug\"></i></span>
          <span class=\"nav\">
          <span class=\"field\">
            <i class=\"tr-icon-code\"></i><span>tr_{$controller}_field(\"{$group}[{$this->name}]\");</span>
          </span>
        </span>
      </div>";
        }

        return $debug;
    }

    private function get_select_html()
    {

        $dir = TR_MATRIX_DIR . '/' . $this->name;

        if (file_exists( $dir )) {

            $files = preg_grep( '/^([^.])/', scandir( $dir ) );

            $select = "<select class=\"matrix-select-{$this->mxid}\">";

            foreach ($files as $f) {
                if (file_exists( $dir . '/' . $f )) {
                    $path = pathinfo( $f );
                    $generator = new Generator();

                    $in_inc_files = ( ! empty( $this->include_files ) && in_array( $f, $this->include_files ) );
                    $no_inc_files = empty( $this->include_files );

                    if ($in_inc_files || $no_inc_files) {
                        $attr = array( 'value'      => $path['filename'],
                                       'data-file'  => $path['filename'],
                                       'data-group' => $this->form->getGroup()
                        );
                        $select .= $generator->newElement( 'option', $attr, $this->clean_file_name( $path['filename'] ) )->getString();
                    }
                }
            }

            $select .= '</select>';

        } else {
            $select = "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> Add a folder for Matrx <code>{$dir}</code> and add your matrix files to it.</div>";
        }

        return $select;

    }

    private function getMatrixBlocks()
    {

        $get_value = new GetValue();
        $val = $get_value->value( $this->form->getGroup() . "[{$this->name}]", $this->form->getItemId(),
        $this->form->getController(), false );

        if (is_array( $val )) {

            foreach ($val as $t => $i) {
                foreach ($i as $type => $v) {

                    $form = $this->form;
                    $form->setDebugStatus(false);
                    $tr_matrix_id    = $t;
                    $tr_matrix_group = $this->name;
                    $tr_matrix_type  = lcfirst( $type );
                    $init_grp        = $form->getGroup();

                    $form->setGroup($init_grp . "[{$tr_matrix_group}][{$tr_matrix_id}][{$tr_matrix_type}]");
                    $path        = TR_MATRIX_DIR . "/" . $this->name . "/{$type}.php";


                        ?>
                        <div class="matrix-field-group tr-repeater-group matrix-type-<?php echo $tr_matrix_type; ?> matrix-group-<?php echo $tr_matrix_group; ?>">
                            <div class="repeater-controls">
                                <div class="collapse"></div>
                                <div class="move"></div>
                                <a href="#remove" class="remove" title="remove"></a>
                            </div>
                            <div class="repeater-inputs">
                                <?php
                                if (file_exists( $path )) {
                                    /** @noinspection PhpIncludeInspection */
                                    include( $path );
                                } else {
                                    echo '<p>No Matrix file found.</p>';
                                }
                                ?>
                            </div>
                        </div>
                        <?php



                    $form->setGroup($init_grp);
                    $form->setDebugStatus(true);
                }
            }

        }

    }

}