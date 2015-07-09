<?php
namespace TypeRocket;

use TypeRocket\Html\Generator as Generator;
use TypeRocket\Html\Tag as Tag;

class Form
{

    public $id = 'tr_the_form';
    public $controller = null;
    public $action = null;
    public $item_id = null;

    /** @var \TypeRocket\Fields\Field $current_field */
    public $current_field = '';
    public $get_values = true;
    private $group = null;
    public $sub = null;
    public $debug = null;
    public $settings = array();

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
        $this->setItem( $item_id );
        $this->autoConfig();

        do_action( 'tr_make_form', $this );

        return $this;
    }

    public function setController( $controller )
    {
        $this->controller = $controller;

        return $this;
    }

    public function setAction( $action )
    {
        $this->action = $action;

        return $this;
    }

    public function setItem( $item_id )
    {
        if (isset( $item_id )) {
            $this->item_id = (int) $item_id;
        }

        return $this;
    }

    public function setId( $id )
    {
        if (is_string( $id )) {
            $this->id = $id;
        }

        return $this;
    }

    public function setGroup( $group )
    {
        if (Validate::bracket( $group )) {
            $this->group = $group;
        }

        return $this;
    }

    public function setSettings( array $settings )
    {
        $this->settings = $settings;

        return $this;
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

            $this->setItem( $item_id );
            $this->setController( $controller );
        }
    }

    public function _e( $v )
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
                'rest-api' => '/typerocket_api/v1/' . $this->controller . '/' . $this->item_id
            );
        }

        $attr = array_merge( $defaults, $attr, $rest );

        $form      = new Tag( 'form', $attr );
        $generator = new Generator();

        $r = $form->getStringOpenTag() . PHP_EOL;
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
     * @param $name
     * @param $settings
     */
    private function setup_field( &$field_obj, $name, &$settings )
    {

        do_action( 'tr_start_setup_field', $this, $field_obj, $name, $settings );

        if (is_string( $this->group ) && empty( $settings['group'] )) {
            $settings['group'] = $this->group;
        }

        if (is_string( $this->sub ) && empty( $settings['sub'] )) {
            $settings['sub'] = $this->sub;
        }

        if (isset( $settings['builtin'] ) && $settings['builtin'] == true) {
            $field_obj->builtin = true;
        }

        $field_obj->connect_form( $this );
        $field_obj->setup( $name, $settings['group'], $settings['sub'] );
        if ( ! isset( $settings['label'] )) {
            $settings['label'] = $name;
        }

        if ($settings['template'] == true) {
            $field_obj->attr['data-name'] = $field_obj->attr['name'];
            unset( $field_obj->attr['name'] );
            unset( $field_obj->attr['id'] );
        }

        do_action( 'tr_end_setup_field', $this, $field_obj, $name, $settings );

    }

    /**
     * @param \TypeRocket\Fields\Field $field_obj
     * @param array $settings
     * @param bool $label
     */
    public function addField( $field_obj, $settings = array(), $label = true )
    {
        $this->current_field           = $field_obj;
        $this->current_field->settings = $settings;
        $this->current_field->label    = $label;
        $field                         = $this->current_field->render();
        $label                         = $this->label();
        $id                            = esc_attr( $this->current_field->settings['id'] );

        if ( ! empty( $id )) {
            $id = "id=\"{$id}\"";
        } else {
            $id = '';
        }

        if (isset( $this->current_field->settings['help'] )) {
            $help = $this->current_field->settings['help'];
            $help =
                "<div class=\"help\">
          <p>{$help}</p>
        </div>";
        } else {
            $help = '';
        }

        if (empty( $this->current_field->settings['html'] ) && $this->current_field->settings['html'] === false) {
            $html = $field;
        } else {

            $html_class = trim( 'control-section ' . apply_filters( 'tr_form_html_class_filter', '',
                    $this->current_field, $this ) );

            $html =
                "<div class=\"{$html_class}\" {$id}>
        {$label}
        <div class=\"control\">
          {$field}{$help}
        </div>
      </div>";
        }
        $this->_e( $html );
        $this->current_field = null;
    }

    private function label()
    {
        $open_html  = "<div class=\"control-label\"><span class=\"label\">";
        $close_html = '</span></div>';
        $debug      = $this->debug();
        $html       = '';

        if ($this->current_field->label !== false) {
            $label = $this->current_field->settings['label'];
            $html  = "{$open_html}{$label} {$debug}{$close_html}";
        } elseif ($debug !== '') {
            $html = "{$open_html}{$debug}{$close_html}";
        }

        return $html;
    }

    private function is_debug()
    {
        return ( $this->debug === false ) ? $this->debug : TR_DEBUG;
    }

    private function debug()
    {
        $html = '';
        if ($this->is_debug() === true && $this->current_field->builtin == false && is_admin() && $this->current_field->debuggable == true) {
            $html =
                "<div class=\"dev\">
        <span class=\"debug\"><i class=\"tr-icon-bug\"></i></span>
          <span class=\"nav\">
          <span class=\"field\">
            <i class=\"tr-icon-code\"></i><span>tr_{$this->controller}_field(\"{$this->current_field->brackets}\");</span>
          </span>
        </span>
      </div>";
        }

        return $html;
    }

    public function division( $headline = 'Division', $description = null )
    {
        $content = "<div class=\"control-division\">";

        $tags = array(
            'a'      => array(
                'href'  => array(),
                'title' => array()
            ),
            'br'     => array(),
            'em'     => array(),
            'strong' => array(),
        );

        if (is_string( $headline )) {
            $headline = wp_kses( $headline, $tags );
            $content .= "<h2>{$headline}</h2>";
        }

        if (is_string( $description )) {
            $description = wp_kses( $description, $tags );
            $content .= "<p>{$description}</p>";
        }

        $content .= "</div>";

        $content = apply_filters( 'tr_from_division', $content, $headline, $description );

        $this->_e( $content );

        return $this;
    }

    public function repeater( $name, $fields, $settings = array(), $label = 'Repeater' )
    {
        $paths = Config::getPaths();
        wp_enqueue_script( 'typerocket-booyah', $paths['urls']['assets'] . '/js/booyah.js', array( 'jquery' ), '1.0',
            true );
        wp_enqueue_script( 'jquery-ui-sortable', array( 'jquery' ), '1.0', true );

        $this->debug = false;

        // add controls
        if (isset( $settings['help'] )) {
            $help = "<div class=\"help\"> <p>{$settings['help']}</p> </div>";
        } else {
            $help = '';
        }

        // add buttom settings
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
        if (TR_DEBUG === true && is_admin()) {
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
        $this->render_fields( $fields, 'template' );
        $this->_e( $templatesContainerEnd );

        // render saved data
        $this->_e( '<div class="tr-repeater-fields">' ); // start tr-repeater-fields
        $getter  = new GetField();
        $repeats = $getter->value( $root_group, $this->item_id, $this->controller );
        if (is_array( $repeats )) {
            foreach ($repeats as $k => $array) {
                $this->_e( '<div class="tr-repeater-group">' );
                $this->_e( $templatesContainer );
                $this->group = $root_group . "[{$k}]";
                $this->render_fields( $fields );
                $this->_e( $templatesContainerEnd );
            }
        }
        $this->_e( '</div>' ); // end tr-repeater-fields
        $this->group = $cache_group;
        $this->sub   = $cache_sub;
        $this->_e( '</div>' ); // end tr-repeater

        $this->debug = null;

    }

    public function render_fields( $fields = array(), $type = null )
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
    }

    public function setup_field_attr( $field, $attr )
    {

        if (array_key_exists( 'class', $attr )) {
            $field->attr['class'] .= ' ' . $attr['class'];
        }

        $field->attr = array_merge( $attr, $field->attr );

        return $field->attr;
    }

    public function text( $name, $attr = array(), $settings = array(), $label = true )
    {
        $field = new Fields\Text();
        $this->setup_field( $field, $name, $settings );
        $field->attr = $this->setup_field_attr( $field, $attr );
        $this->addField( $field, $settings, $label );

        return $this;
    }

    public function input( $type, $name, $attr = array(), $settings = array(), $label = true )
    {
        $field = new Fields\Text();
        $this->setup_field( $field, $name, $settings );
        $field->attr = $this->setup_field_attr( $field, $attr );
        $field->type = $type;
        $this->addField( $field, $settings, $label );

        return $this;
    }

    public function password( $name, $attr = array(), $settings = array(), $label = true )
    {
        $field = new Fields\Text();
        $this->setup_field( $field, $name, $settings );
        $field->attr                 = $this->setup_field_attr( $field, $attr );
        $field->type                 = 'password';
        $field->attr['autocomplete'] = 'off';
        $this->addField( $field, $settings, $label );

        return $this;
    }

    public function hidden( $name, $attr = array(), $settings = array(), $label = false )
    {
        $field = new Fields\Text();
        $this->setup_field( $field, $name, $settings );
        $field->attr      = $this->setup_field_attr( $field, $attr );
        $field->type      = 'hidden';
        $settings['html'] = false;
        $this->addField( $field, $settings, $label );

        return $this;
    }

    public function submit( $name, $attr = array(), $settings = array(), $label = false )
    {
        $field = new Fields\Submit();
        $this->setup_field( $field, $name, $settings );
        $field->attr['value'] = $name;
        $field->attr          = $this->setup_field_attr( $field, $attr );
        $this->addField( $field, $settings, $label );

        return $this;
    }

    public function textarea( $name, $attr = array(), $settings = array(), $label = true )
    {
        $field = new Fields\Textarea();
        $this->setup_field( $field, $name, $settings );
        $field->attr = $this->setup_field_attr( $field, $attr );
        $this->addField( $field, $settings, $label );

        return $this;
    }

    public function radio( $name, $options, $attr = array(), $settings = array(), $label = true )
    {
        $field = new Fields\Radio();
        $this->setup_field( $field, $name, $settings );
        $field->attr    = $this->setup_field_attr( $field, $attr );
        $field->options = $options;
        $this->addField( $field, $settings, $label );

        return $this;
    }

    public function checkbox( $name, $attr = array(), $settings = array(), $label = true )
    {
        $field = new Fields\Checkbox();
        $this->setup_field( $field, $name, $settings );
        $field->attr = $this->setup_field_attr( $field, $attr );
        $this->addField( $field, $settings, $label );

        return $this;
    }

    public function select( $name, $options, $attr = array(), $settings = array(), $label = true )
    {
        $field = new Fields\Select();
        $this->setup_field( $field, $name, $settings );
        $field->attr    = $this->setup_field_attr( $field, $attr );
        $field->options = $options;
        $this->addField( $field, $settings, $label );

        return $this;
    }

    public function wp_editor( $name, $options = array(), $attr = array(), $settings = array(), $label = true )
    {
        $field = new Fields\Editor();
        $this->setup_field( $field, $name, $settings );
        $field->attr    = $this->setup_field_attr( $field, $attr );
        $field->options = $options;
        $this->addField( $field, $settings, $label );

        return $this;
    }

    public function color( $name, $attr = array(), $settings = array(), $label = true )
    {
        $field = new Fields\Color();
        $this->setup_field( $field, $name, $settings );
        $field->attr = $this->setup_field_attr( $field, $attr );
        $this->addField( $field, $settings, $label );

        return $this;
    }

    public function date( $name, $attr = array(), $settings = array(), $label = true )
    {
        $field = new Fields\Date();
        $this->setup_field( $field, $name, $settings );
        $field->attr = $this->setup_field_attr( $field, $attr );
        $this->addField( $field, $settings, $label );

        return $this;
    }

    public function image( $name, $attr = array(), $settings = array(), $label = true )
    {
        $field = new Fields\Image();
        $this->setup_field( $field, $name, $settings );
        $field->attr = $this->setup_field_attr( $field, $attr );
        $this->addField( $field, $settings, $label );

        return $this;
    }

    public function file( $name, $attr = array(), $settings = array(), $label = true )
    {
        $field = new Fields\File();
        $this->setup_field( $field, $name, $settings );
        $field->attr = $this->setup_field_attr( $field, $attr );
        $this->addField( $field, $settings, $label );

        return $this;
    }

    public function gallery( $name, $attr = array(), $settings = array(), $label = true )
    {
        $field = new Fields\Gallery();
        $this->setup_field( $field, $name, $settings );
        $field->attr = $this->setup_field_attr( $field, $attr );
        $this->addField( $field, $settings, $label );

        return $this;
    }

    public function items( $name, $attr = array(), $settings = array(), $label = true )
    {
        $field = new Fields\Items();
        $this->setup_field( $field, $name, $settings );
        $field->attr = $this->setup_field_attr( $field, $attr );
        $this->addField( $field, $settings, $label );

        return $this;
    }

    public function custom( &$field, $name, $attr = array(), $settings = array(), $label = true )
    {
        $this->setup_field( $field, $name, $settings );
        $field->attr = $this->setup_field_attr( $field, $attr );
        $this->addField( $field, $settings, $label );

        return $this;
    }

}
