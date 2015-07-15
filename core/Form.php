<?php
namespace TypeRocket;

use TypeRocket\Html\Generator as Generator,
    TypeRocket\Html\Tag as Tag,
    TypeRocket\Fields\Field as Field;

class Form
{

    private $id = 'tr_the_form';
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

    function __construct()
    {
        $paths = Config::getPaths();
        wp_enqueue_script( 'typerocket-http', $paths['urls']['assets'] . '/js/http.js', array( 'jquery' ), '1', true );
    }

    public function __get( $property )
    {
    }

    public function __set( $property, $value )
    {
    }

    public function make( $controller = 'auto', $action = 'update', $item_id = null )
    {

        $this->setController( $controller );
        $this->setAction( $action );
        $this->setItemId( $item_id );
        $this->autoConfig();

        do_action( 'tr_make_form', $this );

        return $this;
    }

    public function setController( $controller )
    {
        $this->controller = $controller;

        return $this;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setAction( $action )
    {
        $this->action = $action;

        return $this;
    }

    public function setItemId( $item_id )
    {
        $this->item_id = null;

        if (isset( $item_id )) {
            $this->item_id = (int) $item_id;
        }

        return $this;
    }

    public function getItemId()
    {
        return $this->item_id;
    }

    public function setId( $id )
    {
        $this->id = null;

        if (is_string( $id )) {
            $this->id = $id;
        }

        return $this;
    }

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

    public function getGroup()
    {
        return $this->group;
    }

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

    public function getSub()
    {
        return $this->sub;
    }

    public function setSettings( array $settings )
    {
        $this->settings = $settings;

        return $this;
    }

    public function getSettings()
    {
        return $this->settings;
    }

    public function getSetting( $key )
    {
        if( ! array_key_exists($key, $this->settings)) {
            return null;
        }

        return $this->settings[$key];
    }

    public function setSetting( $key, $value )
    {

        $this->settings[$key] = $value;

        return $this;
    }

    public function removeSetting( $key )
    {
        if( array_key_exists($key, $this->settings)) {
            unset($this->settings[$key]);
        }

        return $this;
    }

    public function setPopulate( $populate )
    {
        $this->populate = (bool) $populate;

        return $this;
    }

    function getPopulate()
    {
        return $this->populate;
    }

    function getCurrentField()
    {
        return $this->currentField;
    }

    function setCurrentField( $field )
    {

        $this->currentField = null;

        if ($field instanceof Field) {
            $this->currentField = $field;
        }

        return $this;
    }

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
    }

    private function _e( $v )
    {
        echo $v;
    }

    public function open( $attr = array(), $use_rest = true )
    {

        switch ($this->action) {
            case 'update' :
                $method = 'PUT';
                break;
            case 'create' :
                $method = 'POST';
                break;
            case 'delete' :
                $method = 'DELETE';
                break;
            default :
                $method = 'PUT';
                break;
        }

        $rest     = array();
        $defaults = array(
            'action' => $_SERVER['REQUEST_URI'],
            'method' => $method,
            'id'     => $this->id
        );

        if ($use_rest == true) {
            $rest = array(
                'class'    => 'typerocket-rest-form',
                'rest-api' => home_url() . '/typerocket_rest_api/v1/' . $this->controller . '/' . $this->item_id
            );
        }

        $attr = array_merge( $defaults, $attr, $rest );

        $form      = new Tag( 'form', $attr );
        $generator = new Generator();

        $r = $form->getStringOpenTag();
        $r .= $generator->newInput( 'hidden', '_method', $method )->getString();
        $r .= wp_nonce_field( 'form_' . TR_SEED, '_tr_nonce_form', false, false );

        $this->_e( $r );

        return $this;
    }

    public function close( $value = false )
    {
        $html = '';
        if (is_string( $value )) {
            $generator = new Generator();
            $html .= $generator->newInput( 'submit', '_tr_submit_form', $value,
                array( 'id' => '_tr_submit_form', 'class' => 'button button-primary' ) )->getString();
        }

        $html .= '</form>';
        $this->_e( $html );

        return $this;
    }

    private function getLabel()
    {
        $open_html  = "<div class=\"control-label\"><span class=\"label\">";
        $close_html = '</span></div>';
        $debug      = $this->getDebug();
        $html       = '';

        if ($this->currentField->getLabel()) {
            $label = $this->currentField->getSetting( 'label' );
            $html  = "{$open_html}{$label} {$debug}{$close_html}";
        } elseif ($debug !== '') {
            $html = "{$open_html}{$debug}{$close_html}";
        }

        return $html;
    }

    function getFieldHelpFunction(Fields\Field $field) {

        $brackets = $field->getBrackets();
        $controller = $field->getController();
        $function = "tr_{$controller}_field('{$brackets}');";

        if($field->getBuiltin() && $field->getController() == 'post') {
            $function = 'Builtin as: ' . $field->getName();
        }

        return $function;
    }


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

    function getDebugStatus()
    {
        return ( $this->debugStatus === false ) ? $this->debugStatus : Config::getDebugStatus();
    }

    public function setDebugStatus( $status )
    {
        $this->debugStatus = (bool) $status;
    }

    /**
     * @param Field $field
     *
     * @return $this
     * @internal param Field $field_obj
     *
     */
    private function renderField( Field $field )
    {
        $this->setCurrentField( $field );
        $field = $this->getCurrentField()->getString();
        $label = $this->getLabel();
        $id    = $this->getCurrentField()->getSetting( 'id' );
        $help  = $this->getCurrentField()->getSetting( 'help' );
        $html  = $this->getCurrentField()->getSetting( 'html' );

        $id   = $id ? "id=\"{$id}\"" : '';
        $help = $help ? "<div class=\"help\"><p>{$help}</p></div>" : '';

        if ($html === false) {
            $html = $field;
        } else {
            $type = strtolower(str_ireplace('\\', '-', get_class($this->getCurrentField())));
            $html = "<div class=\"control-section {$type}\" {$id}>{$label}<div class=\"control\">{$field}{$help}</div></div>";
        }

        $this->_e( $html );
        $this->currentField = null;

        return $this;
    }

    public function renderFields( array $fields = array() )
    {
        foreach ($fields as $functionSetup) {

            $function   = array_shift( $functionSetup );
            $parameters = array_pop( $functionSetup );

            if (method_exists( $this, $function ) && is_array( $parameters )) {
                call_user_func_array( array( $this, $function ), $parameters );
            }

        }

        return $this;
    }


    public function text( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Text();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->renderField( $field );

        return $this;
    }

    public function input( $type, $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Text();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label )->setType( $type );
        $this->renderField( $field );

        return $this;
    }

    public function password( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Text();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $field->setType( 'password' )->setAttribute( 'autocomplete', 'off' );
        $this->renderField( $field );

        return $this;
    }

    public function hidden( $name, array $attr = array(), array $settings = array(), $label = false )
    {
        $field = new Fields\Text();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $field->setType( 'hidden' )->setAttribute( 'html', false );
        $this->renderField( $field );

        return $this;
    }

    public function submit( $name, array $attr = array(), array $settings = array(), $label = false )
    {
        $field = new Fields\Submit();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $field->setAttribute( 'value', $name );
        $this->renderField( $field );

        return $this;
    }

    public function textarea( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Textarea();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->renderField( $field );

        return $this;
    }

    public function editor( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Editor();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->renderField( $field );

        return $this;
    }

    public function radio( $name, array $options, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Radio();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $field->options = $options;
        $this->renderField( $field );

        return $this;
    }

    public function checkbox( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Checkbox();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->renderField( $field );

        return $this;
    }

    public function select( $name, array $options, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Select();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $field->options = $options;
        $this->renderField( $field );

        return $this;
    }

    public function wpEditor(
        $name,
        array $options = array(),
        array $attr = array(),
        array $settings = array(),
        $label = true
    ) {
        $field = new Fields\WordPressEditor();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $field->options = $options;
        $this->renderField( $field );

        return $this;
    }

    public function color( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Color();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->renderField( $field );

        return $this;
    }

    public function date( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Date();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->renderField( $field );

        return $this;
    }

    public function image( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Image();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->renderField( $field );

        return $this;
    }

    public function file( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\File();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->renderField( $field );

        return $this;
    }

    public function gallery( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Gallery();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->renderField( $field );

        return $this;
    }

    public function items( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Items();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->renderField( $field );

        return $this;
    }

    public function matrix(
        $name,
        array $options = array(),
        array $attr = array(),
        array $settings = array(),
        $label = true
    ) {
        $field = new Fields\Matrix();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $field->options = $options;
        $this->renderField( $field );

        return $this;
    }

    public function repeater( $name, array $fields, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Repeater();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $field->fields = $fields;
        $this->renderField( $field );

        return $this;
    }

    /**
     * @param Fields\Field $field
     * @param string $name the name of the field
     * @param array $attr
     * @param array $settings
     * @param bool|true $label show the label
     *
     * @return $this
     */
    public function renderCustomField(
        Field $field,
        $name,
        array $attr = array(),
        array $settings = array(),
        $label = true
    ) {
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->renderField( $field );

        return $this;
    }

}
