<?php
namespace TypeRocket\Fields;

use \TypeRocket\Traits\OptionsTrait,
    \TypeRocket\Html\Generator,
    \TypeRocket\Config,
    \TypeRocket\Buffer,
    \TypeRocket\Sanitize;

class Matrix extends Field implements ScriptField {

    use OptionsTrait;

    protected $mxid = null;
    protected $componentFolder = null;

    /**
     * Run on construction
     */
    protected function init()
    {
        $this->mxid = md5( microtime( true ) ); // set id for matrix random
        $this->setType( 'matrix' );
    }

    /**
     * Get the scripts
     */
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
     * Covert Matrix to HTML string
     */
    public function getString()
    {
        $this->setAttribute('name', $this->getNameAttributeString());

        // setup select list of files
        $select = $this->getSelectHtml();
        $folder = $this->getComponentFolder();
        $group = $this->getName();
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
<input type=\"button\" value=\"Add New\" data-id='{$this->mxid}' data-group='{$group}' data-folder='{$folder}' class=\"button matrix-button\">
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

    /**
     * Sanitize the file name for component
     *
     * @param $name
     *
     * @return string
     */
    private function cleanFileName( $name )
    {

        $name = Sanitize::underscore($name);

        return ucwords( $name );
    }

    /**
     * Set the select list for components
     *
     * @return string
     */
    private function getSelectHtml()
    {

        $name = $this->getName();
        $folder = $this->getComponentFolder();
        $options = $this->getOptions();
        $options = $options ? $options : $this->setOptionsFromFolder()->getOptions();

        if ($options) {
            $generator = new Generator();
            $generator->newElement( 'select', [
                'data-mxid' => $this->mxid,
                'class' => "matrix-select-{$name}",
                'data-group' => $this->getForm()->getGroup()
            ]);
            $default = $this->getSetting('default');

            foreach ($options as $name => $value) {

                $attr['value'] = $value;
                if ( $default == $value && isset($default) ) {
                    $attr['selected'] = 'selected';
                } else {
                    unset( $attr['selected'] );
                }

                $generator->appendInside( 'option', $attr, $name );
            }

            $select = $generator->getString();

        } else {

            $paths = Config::getPaths();
            $dir = $paths['components'] . '/' . $folder;

            $select = "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> Add a files for Matrix <code>{$dir}</code> and add your matrix files to it.</div>";
        }

        return $select;

    }

    /**
     * Set options from folder
     *
     * @return $this
     */
    public function setOptionsFromFolder() {
        $paths = Config::getPaths();
        $folder = $this->getComponentFolder();
        $dir = $paths['components'] . '/' . $folder;

        if (file_exists( $dir )) {

            $files = preg_grep( '/^([^.])/', scandir( $dir ) );

            foreach ($files as $file) {

                $is_php_file = function($haystack) {
                    // search forward starting from end minus needle length characters
                    $needle = '.php';
                    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
                };

                if (file_exists( $dir . '/' . $file ) && $is_php_file($file) ) {

                    $the_file = $file;
                    $path = pathinfo( $file );
                    $key = $this->cleanFileName( $path['filename'] );
                    $line = fgets(fopen( $dir . '/' . $the_file, 'r'));
                    if( preg_match("/<[h|H]\\d>(.*)<\\/[h|H]\\d>/U", $line, $matches) ) {
                        $key = strip_tags($matches[1]);
                    }
                    $this->options[$key] = $path['filename'];
                }
            }

        }

        return $this;
    }

    /**
     * Get component block
     *
     * @return string
     */
    private function getMatrixBlocks()
    {

        $val = $this->getValue();
        $utility = new Buffer();
        $blocks = '';
        $form = $this->getForm();
        $paths = Config::getPaths();
        $folder = $this->getComponentFolder();

        if (is_array( $val )) {

            $utility->startBuffer();

            foreach ($val as $tr_matrix_key => $data) {
                foreach ($data as $tr_matrix_type => $fields) {

                    $tr_matrix_group = $this->getName();
                    $tr_matrix_type  = lcfirst( $tr_matrix_type );
                    $root_group        = $form->getGroup();
                    $form->setDebugStatus(false);
                    $append_group = $root_group;

                    if($root_group) {
                        $append_group .= '.';
                    }

                    $form->setGroup($append_group . "{$tr_matrix_group}.{$tr_matrix_key}.{$tr_matrix_type}");
                    $file        = $paths['components'] . "/" . $folder . "/{$tr_matrix_type}.php";
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

    /**
     * Get component folder
     *
     * @return null|string
     */
    public function getComponentFolder() {

        if( ! $this->componentFolder ) {
            $this->componentFolder = $this->getName();
        }

        return $this->componentFolder;
    }

    /**
     * Set component folder
     *
     * @param string $folder_name
     *
     * @return $this
     */
    public function setComponentFolder($folder_name = '') {

        $paths = Config::getPaths();
        $dir = $paths['components'] . '/' . $folder_name;

        if(file_exists($dir)) {
            $this->componentFolder = $folder_name;
        }

        return $this;
    }

}