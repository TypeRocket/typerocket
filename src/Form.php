<?php
namespace TypeRocket;

use TypeRocket\Html\Generator as Generator,
    TypeRocket\Html\Tag as Tag,
    TypeRocket\Fields\Field as Field;

class Form
{

    private $controller = null;
    private $action = null;
    private $item_id = null;

    /** @var \TypeRocket\Fields\Field $currentField */
    private $currentField = '';
    private $populate = true;
    private $group = null;
    private $sub = null;
    private $debugStatus = null;
    private $settings = array();

    public function __get( $property )
    {
    }

    public function __set( $property, $value )
    {
    }

    /**
     * Instance the From
     *
     * @param string $controller posts, users, comments or options
     * @param string $action update or create
     * @param null|int $item_id you can set this to null or an integer
     */
    public function __construct( $controller = 'auto', $action = 'update', $item_id = null )
    {
        $paths = Config::getPaths();
        wp_enqueue_script( 'typerocket-http', $paths['urls']['assets'] . '/js/http.js', array( 'jquery' ), '1', true );

        $this->setController( $controller );
        $this->setAction( $action );
        $this->setItemId( $item_id );
        $this->autoConfig();

        do_action( 'tr_make_form', $this );
    }

    /**
     * Set controller
     *
     * @param $controller
     *
     * @return $this
     */
    public function setController( $controller )
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * Get controller
     *
     * @return null
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set Action
     *
     * @param $action
     *
     * @return $this
     */
    public function setAction( $action )
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Set Item ID
     *
     * @param $item_id
     *
     * @return $this
     */
    public function setItemId( $item_id )
    {
        $this->item_id = null;

        if (isset( $item_id )) {
            $this->item_id = (int) $item_id;
        }

        return $this;
    }

    /**
     * Get Item ID
     *
     * @return null
     */
    public function getItemId()
    {
        return $this->item_id;
    }

    /**
     * Set Group into bracket syntax
     *
     * @param $group
     *
     * @return $this
     */
    public function setGroup( $group )
    {
        $this->group = null;

        if (Validate::bracket( $group )) {
            $this->group = $group;
        } elseif (is_string( $group )) {
            $this->group = "[{$group}]";
        }

        return $this;
    }

    /**
     * Get Group
     *
     * @return null
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set Sub Group into bracket syntax
     *
     * @param $sub
     *
     * @return $this
     */
    public function setSub( $sub )
    {
        $this->sub = null;

        if (Validate::bracket( $sub )) {
            $this->sub = $sub;
        } elseif (is_string( $sub )) {
            $this->sub = "[{$sub}]";
        }

        return $this;
    }

    /**
     * Get Sub Group
     *
     * @return null
     */
    public function getSub()
    {
        return $this->sub;
    }

    /**
     * Set From settings
     *
     * @param array $settings
     *
     * @return $this
     */
    public function setSettings( array $settings )
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Get Form settings
     *
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Get From setting by key
     *
     * @param $key
     *
     * @return null
     */
    public function getSetting( $key )
    {
        if ( ! array_key_exists( $key, $this->settings )) {
            return null;
        }

        return $this->settings[$key];
    }


    /**
     * Set Form setting by key
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setSetting( $key, $value )
    {

        $this->settings[$key] = $value;

        return $this;
    }

    /**
     * Remove setting bby key
     *
     * @param $key
     *
     * @return $this
     */
    public function removeSetting( $key )
    {
        if (array_key_exists( $key, $this->settings )) {
            unset( $this->settings[$key] );
        }

        return $this;
    }

    /**
     * Get the render setting of the form
     *
     * @return null
     */
    public function getRenderSetting()
    {
        if ( ! array_key_exists( 'render', $this->settings )) {
            return null;
        }

        return $this->settings['render'];
    }

    /**
     * Render Setting
     *
     * By setting render to 'raw' the form will not add any special html wrappers.
     * You have more control of the design when render is set to raw.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setRenderSetting( $value )
    {

        $this->settings['render'] = $value;

        return $this;
    }

    /**
     * Set whether to populate fields in the form. If set to false fields will
     * always be left empty and with their default values.
     *
     * @param $populate
     *
     * @return $this
     */
    public function setPopulate( $populate )
    {
        $this->populate = (bool) $populate;

        return $this;
    }

    /**
     * Get Populate
     *
     * @return bool
     */
    function getPopulate()
    {
        return $this->populate;
    }

    /**
     * Get the current Field the From is processing
     *
     * @return Field
     */
    function getCurrentField()
    {
        return $this->currentField;
    }

    /**
     * Set the current Field to process
     *
     * @param Field $field
     *
     * @return $this
     */
    function setCurrentField( Field $field )
    {

        $this->currentField = null;

        if ($field instanceof Field) {
            $this->currentField = $field;
        }

        return $this;
    }

    /**
     * Auto config form is no Controller etc. is set
     *
     * @return $this
     */
    private function autoConfig()
    {
        if ($this->controller === 'auto') {
            global $post, $comment, $user_id;

            if (isset( $post->ID )) {
                $item_id    = $post->ID;
                $controller = 'posts';
            } elseif (isset( $comment->comment_ID )) {
                $item_id    = $comment->comment_ID;
                $controller = 'comments';
            } elseif (isset( $user_id )) {
                $item_id    = $user_id;
                $controller = 'users';
            } else {
                $item_id    = null;
                $controller = 'options';
            }

            $this->setItemId( $item_id );
            $this->setController( $controller );
        }

        return $this;
    }

    /**
     * Open Form Element
     *
     * Not needed post types, for example, since WordPress already opens this for you.
     *
     * @param array $attr
     * @param bool|true $use_rest
     *
     * @return $this
     */
    public function open( $attr = array(), $use_rest = true )
    {

        switch ($this->action) {
            case 'update' :
                $method = 'PUT';
                break;
            case 'create' :
                $method = 'POST';
                break;
            default :
                $method = 'PUT';
                break;
        }

        $rest     = array();
        $defaults = array(
            'action'      => $_SERVER['REQUEST_URI'],
            'method'      => 'POST',
            'data-method' => $method
        );

        if ($use_rest == true) {
            $rest = array(
                'class'    => 'typerocket-rest-form',
                'data-api' => home_url() . '/typerocket_rest_api/v1/' . $this->controller . '/' . $this->item_id
            );
        }

        $attr = array_merge( $defaults, $attr, $rest );

        $form      = new Tag( 'form', $attr );
        $generator = new Generator();

        $r = $form->getStringOpenTag();
        $r .= $generator->newInput( 'hidden', '_method', $method )->getString();
        $r .= wp_nonce_field( 'form_' . TR_SEED, '_tr_nonce_form', false, false );

        return $r;
    }

    /**
     * Close the From Element and add a submit button if value is string
     *
     * @param null|string $value
     *
     * @return $this
     */
    public function close( $value = null )
    {
        $html = '';
        if (is_string( $value )) {
            $generator = new Generator();
            $html .= $generator->newInput( 'submit', '_tr_submit_form', $value,
                array( 'id' => '_tr_submit_form', 'class' => 'button button-primary' ) )->getString();
        }

        $html .= '</form>';

        return $html;
    }

    /**
     * Get the Form Field Label
     *
     * @return string
     */
    private function getLabel()
    {
        $open_html  = "<div class=\"control-label\"><span class=\"label\">";
        $close_html = '</span></div>';
        $debug      = $this->getDebug();
        $html       = '';
        $label      = $this->currentField->getLabelOption();

        if ($label) {
            $label = $this->currentField->getSetting( 'label' );
            $html  = "{$open_html}{$label} {$debug}{$close_html}";
        } elseif ($debug !== '') {
            $html = "{$open_html}{$debug}{$close_html}";
        }

        return $html;
    }

    /**
     * Get the debug mode helper content
     *
     * @param Field $field
     *
     * @return string
     */
    private function getFieldHelpFunction( Fields\Field $field )
    {

        $brackets   = $field->getBrackets();
        $controller = $field->getController();
        $function   = "tr_{$controller}_field('{$brackets}');";

        if ($field->getBuiltin() && $field->getController() == 'post') {
            $function = 'Builtin as: ' . $field->getName();
        }

        return $function;
    }

    /**
     * Get the debug HTML for the From Field Label
     *
     * @return string
     */
    private function getDebug()
    {
        $generator = new Generator();
        $html      = '';
        if ($this->getDebugStatus() === true) {
            $dev_html = $this->getFieldHelpFunction( $this->currentField );

            $generator->newElement( 'div', array( 'class' => 'dev' ), '<i class="tr-icon-bug"></i>' );
            $navTag       = new Tag( 'span', array( 'class' => 'nav' ) );
            $fieldCopyTag = new Tag( 'span', array( 'class' => 'field' ), $dev_html );
            $navTag->appendInnerTag( $fieldCopyTag );
            $html = $generator->appendInside( $navTag )->getString();
        }

        return $html;
    }

    /**
     * Get the From debug status
     *
     * @return bool|null
     */
    function getDebugStatus()
    {
        return ( $this->debugStatus === false ) ? $this->debugStatus : Config::getDebugStatus();
    }

    /**
     * Set the form debug status
     *
     * @param bool $status
     *
     * @return $this
     */
    public function setDebugStatus( $status )
    {
        $this->debugStatus = (bool) $status;

        return $this;
    }

    /**
     * Get Form Field string
     *
     * @param Field $field
     *
     * @return string
     */
    public function getFromFieldString( Field $field )
    {
        $this->setCurrentField( $field );
        $field     = $field->getString();
        $label     = $this->getLabel();
        $id        = $this->getCurrentField()->getSetting( 'id' );
        $help      = $this->getCurrentField()->getSetting( 'help' );
        $fieldHtml = $this->getCurrentField()->getSetting( 'render' );
        $formHtml  = $this->getSetting( 'render' );

        $id   = $id ? "id=\"{$id}\"" : '';
        $help = $help ? "<div class=\"help\"><p>{$help}</p></div>" : '';

        if ($fieldHtml == 'raw' || $formHtml == 'raw') {
            $html = $field;
        } else {
            $type = strtolower( str_ireplace( '\\', '-', get_class( $this->getCurrentField() ) ) );
            $html = "<div class=\"control-section {$type}\" {$id}>{$label}<div class=\"control\">{$field}{$help}</div></div>";
        }

        $html = apply_filters( 'tr_from_field_html', $html, $this );
        $this->currentField = null;

        return $html;
    }

    /**
     * Get From fields string from array
     *
     * @param array $fields
     *
     * @return string
     */
    public function getFromFieldsString( array $fields = array() )
    {
        $html = '';

        foreach ($fields as $functionSetup) {

            $function   = array_shift( $functionSetup );
            $parameters = array_pop( $functionSetup );

            if (method_exists( $this, $function ) && is_array( $parameters )) {
                $html .= (string) call_user_func_array( array( $this, $function ), $parameters );
            }

        }

        return $html;
    }

    /**
     * Text Input
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Text
     */
    public function text( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Text( $name, $attr, $settings, $label, $this );
    }

    /**
     * Password Input
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Text
     */
    public function password( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Text( $name, $attr, $settings, $label, $this );
        $field->setType( 'password' )->setAttribute( 'autocomplete', 'off' );

        return $field;
    }

    /**
     * Hidden Input
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|false $label
     *
     * @return Fields\Text
     */
    public function hidden( $name, array $attr = array(), array $settings = array(), $label = false )
    {
        $field = new Fields\Text( $name, $attr, $settings, $label, $this );
        $field->setType( 'hidden' )->setRender( 'raw' );

        return $field;
    }

    /**
     * Submit Button
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|false $label
     *
     * @return Fields\Submit
     */
    public function submit( $name, array $attr = array(), array $settings = array(), $label = false )
    {
        $field = new Fields\Submit( $name, $attr, $settings, $label, $this );
        $field->setAttribute( 'value', $name );

        return $field;
    }

    /**
     * Textarea Input
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Textarea
     */
    public function textarea( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Textarea( $name, $attr, $settings, $label, $this );
    }

    /**
     * Editor Input
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Editor
     */
    public function editor( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Editor( $name, $attr, $settings, $label, $this );
    }

    /**
     * Radio Input
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Radio
     */
    public function radio( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Radio( $name, $attr, $settings, $label, $this );
    }

    /**
     * Checkbox Input
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Checkbox
     */
    public function checkbox( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Checkbox( $name, $attr, $settings, $label, $this );
    }

    /**
     * Select Input
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Select
     */
    public function select( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Select( $name, $attr, $settings, $label, $this );
    }

    /**
     * WordPress Editor
     *
     * Use this only once per page. The WordPress Editor is very buggy. You cannot use
     * this in Metaboxes and repeatable sections.
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\WordPressEditor
     */
    public function wpEditor( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\WordPressEditor( $name, $attr, $settings, $label, $this );
    }

    /**
     * Color Input
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Color
     */
    public function color( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Color( $name, $attr, $settings, $label, $this );
    }

    /**
     * Date Input
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Date
     */
    public function date( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Date( $name, $attr, $settings, $label, $this );
    }

    /**
     * Image Input
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Image
     */
    public function image( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Image( $name, $attr, $settings, $label, $this );
    }

    /**
     * File Input
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\File
     */
    public function file( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\File( $name, $attr, $settings, $label, $this );
    }

    /**
     * Gallery Input
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Gallery
     */
    public function gallery( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Gallery( $name, $attr, $settings, $label, $this );
    }

    /**
     * Items Input
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Items
     */
    public function items( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Items( $name, $attr, $settings, $label, $this );
    }

    /**
     * Matrix Input
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Matrix
     */
    public function matrix(
        $name,
        array $attr = array(),
        array $settings = array(),
        $label = true
    ) {
        return new Fields\Matrix( $name, $attr, $settings, $label, $this );
    }

    /**
     * Repeater Input
     *
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return Fields\Repeater
     */
    public function repeater( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        return new Fields\Repeater( $name, $attr, $settings, $label, $this );
    }

    /**
     * Field object into input
     *
     * @param Fields\Field $field
     *
     * @return Field $field
     */
    public function field( Field $field )
    {
        return $field;
    }

}
