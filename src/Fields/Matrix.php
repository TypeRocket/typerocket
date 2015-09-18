<?php
namespace TypeRocket\Fields;

use TypeRocket\Html\Generator,
    TypeRocket\Config,
    TypeRocket\Buffer,
    \TypeRocket\Sanitize;

class Matrix extends Field implements OptionField, ScriptField {

    private $mxid = null;
    private $options = null;

    /**
     * Run on construction
     */
    protected function init()
    {
        $this->mxid = md5( microtime( true ) ); // set id for matrix random
        $this->setType( 'matrix' );
    }

    public function enqueueScripts() {
        $paths = Config::getPaths();
        // load everything :(
        wp_enqueue_script( 'typerocket-booyah', $paths['urls']['assets'] . '/js/booyah.js', array( 'jquery' ),
            '1.0', true );
        wp_enqueue_script( 'jquery-ui-sortable', array( 'jquery' ), '1.0', true );

        // date
        wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ), '1.0', true );

        // color
        wp_enqueue_script( 'wp-color-picker' );

        // images and gallery
        wp_enqueue_media();
        wp_enqueue_script( 'typerocket-media', $paths['urls']['assets'] . '/js/media.js', array( 'jquery' ), '1.0',
            true );
        wp_enqueue_script( 'typerocket-items', $paths['urls']['assets'] . '/js/items.js',
            array( 'jquery' ), '1.0', true );

        // editor
        wp_enqueue_script( 'typerocket-editor', $paths['urls']['assets'] . '/js/redactor.min.js', array( 'jquery' ), '1.0',
            true );

        // load just matrix :)
        wp_enqueue_script( 'tr-matrix-core', $paths['urls']['assets'] . '/js/matrix.js', array( 'jquery' ), '1.0', true );
    }

    /**
     * Covert Matrix to HTML string
     */
    public function getString()
    {
        $this->setAttribute('name', $this->getNameAttributeString());

        // setup select list of files
        $select = $this->getSelectHtml();
        $name = $this->getName();
        $settings = $this->getSettings();
        $blocks = $this->getMatrixBlocks();

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

    private function cleanFileName( $name )
    {

        $name = Sanitize::underscore($name);
        $name = str_replace( '-', ' ', $name );

        return ucwords( $name );
    }

    private function getSelectHtml()
    {

        $name = $this->getName();
        $options = $this->getOptions();
        $options = $options ? $options : $this->setOptionsFromFolder()->getOptions();

        if ($options) {
            $generator = new Generator();
            $generator->newElement( 'select', array(
                'data-mxid' => $this->mxid,
                'class' => "matrix-select-{$name}",
                'data-group' => $this->getForm()->getGroup()
            ) );
            $default = $this->getSetting('default');

            foreach ($options as $name => $value) {

                $attr['value'] = $value;
                if ($default === $value) {
                    $attr['selected'] = 'selected';
                } else {
                    unset( $attr['selected'] );
                }

                $generator->appendInside( 'option', $attr, $name );
            }

            $select = $generator->getString();

        } else {

            $paths = Config::getPaths();
            $dir = $paths['matrix'] . '/' . $name;

            $select = "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> Add a files for Matrix <code>{$dir}</code> and add your matrix files to it.</div>";
        }

        return $select;

    }

    public function setOptionsFromFolder() {
        $paths = Config::getPaths();
        $name = $this->getName();
        $dir = $paths['matrix'] . '/' . $name;

        if (file_exists( $dir )) {

            $files = preg_grep( '/^([^.])/', scandir( $dir ) );

            foreach ($files as $file) {
                if (file_exists( $dir . '/' . $file )) {
                    $path = pathinfo( $file );
                    $key = $this->cleanFileName( $path['filename'] );
                    $this->options[$key] = $path['filename'];
                }
            }

        }

        return $this;
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

    public function setOption( $key, $value )
    {
        $this->options[ $key ] = $value;

        return $this;
    }

    public function setOptions( $options )
    {
        $this->options = $options;

        return $this;
    }

    public function getOption( $key, $default = null )
    {
        if ( ! array_key_exists( $key, $this->options ) ) {
            return $default;
        }

        return $this->options[ $key ];
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function removeOption( $key )
    {
        if ( array_key_exists( $key, $this->options ) ) {
            unset( $this->options[ $key ] );
        }

        return $this;
    }

}