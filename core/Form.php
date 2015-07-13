<?php
namespace TypeRocket;

use TypeRocket\Html\Generator as Generator;
use TypeRocket\Html\Tag as Tag;

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
        wp_enqueue_script( 'typerocket-scripts', $paths['urls']['assets'] . '/js/typerocket.js', array( 'jquery' ), '1',
            true );
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
        } elseif(is_string($group)) {
            $this->group = "[{$group}]";
        }

        return $this;
    }

    public function getGroup() {
        return $this->group;
    }

    public function setSub( $sub )
    {
        $this->sub = null;

        if (Validate::bracket( $sub )) {
            $this->sub = $sub;
        }

        return $this;
    }

    public function getSub() {
        return $this->sub;
    }

    public function setSettings( array $settings )
    {
        $this->settings = $settings;

        return $this;
    }

    public function setPopulate($populate) {
        $this->populate = (bool) $populate;

        return $this;
    }

    function getPopulate() {
        return $this->populate;
    }

    private function autoConfig()
    {
        if ($this->controller === 'auto') {
            global $post, $comment, $user_id;

            if (isset( $post->ID )) {
                $item_id    = $post->ID;
                $controller = 'post';
            } elseif (isset( $comment->comment_ID )) {
                $item_id    = $comment->comment_ID;
                $controller = 'comment';
            } elseif (isset( $user_id )) {
                $item_id    = $user_id;
                $controller = 'user';
            } else {
                $item_id    = null;
                $controller = 'option';
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

    /**
     * @param \TypeRocket\Fields\Field $field_obj
     *
     * @return $this
     */
    private function addField( $field_obj )
    {
        $this->currentField           = $field_obj;
        $field                         = $this->currentField->render();
        $label                         = $this->label();
        $id                            = esc_attr( $this->currentField->getSetting('id') );

        if ( ! empty( $id )) {
            $id = "id=\"{$id}\"";
        } else {
            $id = '';
        }

        if ( $this->currentField->getSetting('help') ) {
            $help = $this->currentField->getSetting('help');
            $help =
                "<div class=\"help\">
          <p>{$help}</p>
        </div>";
        } else {
            $help = '';
        }

        if ( $this->currentField->getSetting('html') === false) {
            $html = $field;
        } else {

            $html_class = trim( 'control-section ' . apply_filters( 'tr_form_html_class_filter', '',
                    $this->currentField, $this ) );

            $html =
                "<div class=\"{$html_class}\" {$id}>
        {$label}
        <div class=\"control\">
          {$field}{$help}
        </div>
      </div>";
        }
        $this->_e( $html );
        $this->currentField = null;

        return $this;
    }

    private function label()
    {
        $open_html  = "<div class=\"control-label\"><span class=\"label\">";
        $close_html = '</span></div>';
        $debug      = $this->debug();
        $html       = '';

        if ($this->currentField->getLabel()) {
            $label = $this->currentField->getSetting('label');
            $html  = "{$open_html}{$label} {$debug}{$close_html}";
        } elseif ($debug !== '') {
            $html = "{$open_html}{$debug}{$close_html}";
        }

        return $html;
    }

    function getDebugStatus()
    {
        return ( $this->debugStatus === false ) ? $this->debugStatus : Config::getDebugStatus();
    }

    public function setDebugStatus($status) {
        $this->debugStatus = (bool) $status;
    }

    private function debug()
    {
        $generator = new Generator();
        $html = '';
        if ($this->getDebugStatus() === true ) {

            $dev = new Dev();

            $generator->newElement('div', array('class' => 'dev'), '<i class="tr-icon-info"></i>');
            $navTag = new Tag('span', array('class' => 'nav'));
            $fieldCopyTag = new Tag('span', array('class' => 'field'), $dev->getFieldHelpFunction($this->currentField));
            $navTag->appendInnerTag($fieldCopyTag);
            $html = $generator->appendInside($navTag)->getString();
        }

        return $html;
    }

    public function repeater( $name, $fields, array $settings = array(), $label = 'Repeater' )
    {
        $paths = Config::getPaths();
        wp_enqueue_script( 'typerocket-booyah', $paths['urls']['assets'] . '/js/booyah.js', array( 'jquery' ), '1.0',
            true );
        wp_enqueue_script( 'jquery-ui-sortable', array( 'jquery' ), '1.0', true );

        $this->setDebugStatus(false);

        // add controls
        if (isset( $settings['help'] )) {
            $help = "<div class=\"help\"> <p>{$settings['help']}</p> </div>";
        } else {
            $help = '';
        }

        // add button settings
        if (isset( $settings['add_button'] )) {
            $add_button_value = $settings['add_button'];
        } else {
            $add_button_value = "Add New";
        }

        // add label
        if (is_string( $label )) {
            $label = "<div class=\"control-label\"><span class=\"label\">{$label}</span></div>";
        }

        // template for repeater groups
        $href                  = '#remove';
        $templatesContainer    = '<div class="repeater-controls"><div class="collapse"></div><div class="move"></div><a href="' . $href . '" class="remove" title="remove"></a></div><div class="repeater-inputs">';
        $templatesContainerEnd = '</div></div>';

        $this->_e( '<div class="control-section tr-repeater">' ); // start tr-repeater

        // setup repeater
        $cache_group = $this->group;
        $cache_sub   = $this->sub;

        $utility = new Utility();
        $utility->sanitize_string( $name );
        $root_group = $this->group .= "[{$name}]";
        $this->group .= "[{{ {$name} }}]";

        // debug
        $debug = '';
        if ( $this->getDebugStatus() === true ) {
            $debug =
                "<div class=\"dev\">
        <span class=\"debug\"><i class=\"tr-icon-bug\"></i></span>
          <span class=\"nav\">
          <span class=\"field\">
            <i class=\"tr-icon-code\"></i><span>tr_{$this->controller}_field(\"{$root_group}\");</span>
          </span>
        </span>
      </div>";
        }

        $this->_e( $debug );

        $this->_e( $label );

        // add controls (add, flip, clear all)
        $this->_e( "<div class=\"controls\"><div class=\"tr-repeater-button-add\"><input type=\"button\" value=\"{$add_button_value}\" class=\"button add\" /></div><div class=\"button-group\"><input type=\"button\" value=\"Flip\" class=\"flip button\" /><input type=\"button\" value=\"Contract\" class=\"tr_action_collapse button\"><input type=\"button\" value=\"Clear All\" class=\"clear button\" /></div>{$help}</div>" );

        // render js template data
        $this->_e( '<div class="tr-repeater-group-template" data-id="' . $name . '">' );
        $this->_e( $templatesContainer );
        $this->renderFields( $fields, 'template' );
        $this->_e( $templatesContainerEnd );

        // render saved data
        $this->_e( '<div class="tr-repeater-fields">' ); // start tr-repeater-fields
        $getter  = new GetValue();
        $repeats = $getter->value( $root_group, $this->item_id, $this->controller );
        if (is_array( $repeats )) {
            foreach ($repeats as $k => $array) {
                $this->_e( '<div class="tr-repeater-group">' );
                $this->_e( $templatesContainer );
                $this->group = $root_group . "[{$k}]";
                $this->renderFields( $fields );
                $this->_e( $templatesContainerEnd );
            }
        }
        $this->_e( '</div>' ); // end tr-repeater-fields
        $this->group = $cache_group;
        $this->sub   = $cache_sub;
        $this->_e( '</div>' ); // end tr-repeater

        $this->setDebugStatus(null);

        return $this;
    }

    public function renderFields( array $fields = array(), $type = null )
    {
        foreach ($fields as $args) {

            if (empty( $args[1][1] )) {
                $args[1][1] = array();
            }
            if (empty( $args[1][2] )) {
                $args[1][2] = array();
            }

            if ($args[0] == 'select' || $args[0] == 'radio' || $args[0] == 'custom') {
                if (empty( $args[1][3] )) {
                    $args[1][3] = array();
                }
                if (is_string( $type )) {
                    $args[1][3][$type] = true;
                }
                call_user_func_array( array( $this, $args[0] ), $args[1] );
            } else {
                if (is_string( $type )) {
                    $args[1][2][$type] = true;
                }
                call_user_func_array( array( $this, $args[0] ), $args[1] );
            }

        }

        return $this;
    }


    public function text( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Text();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->addField( $field );

        return $this;
    }

    public function input( $type, $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Text();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label )->setType($type);
        $this->addField( $field );

        return $this;
    }

    public function password( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Text();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $field->setType('password')->setAttribute('autocomplete', 'off');
        $this->addField( $field );

        return $this;
    }

    public function hidden( $name, array $attr = array(), array $settings = array(), $label = false )
    {
        $field = new Fields\Text();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $field->setType('hidden')->setAttribute('html', false);
        $this->addField( $field );

        return $this;
    }

    public function submit( $name, array $attr = array(), array $settings = array(), $label = false )
    {
        $field = new Fields\Submit();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $field->setAttribute('value', $name);
        $this->addField( $field );

        return $this;
    }

    public function textarea( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Textarea();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->addField( $field );

        return $this;
    }

    public function radio( $name, array $options, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Radio();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $field->options = $options;
        $this->addField( $field );

        return $this;
    }

    public function checkbox( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Checkbox();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->addField( $field );

        return $this;
    }

    public function select( $name, array $options, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Select();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $field->options = $options;
        $this->addField( $field );

        return $this;
    }

    public function wp_editor( $name, array $options = array(), array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Editor();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $field->options = $options;
        $this->addField( $field );

        return $this;
    }

    public function color( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Color();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->addField( $field );

        return $this;
    }

    public function date( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Date();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->addField( $field );

        return $this;
    }

    public function image( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Image();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->addField( $field );

        return $this;
    }

    public function file( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\File();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->addField( $field );

        return $this;
    }

    public function gallery( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Gallery();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->addField( $field );

        return $this;
    }

    public function items( $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field = new Fields\Items();
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->addField( $field );

        return $this;
    }

    /**
     * @param Fields\Field $field
     * @param $name
     * @param array $attr
     * @param array $settings
     * @param bool|true $label
     *
     * @return $this
     */
    public function renderCustomField( $field, $name, array $attr = array(), array $settings = array(), $label = true )
    {
        $field->setupByForm( $this )->setup( $name, $attr, $settings, $label );
        $this->addField( $field );

        return $this;
    }

}
