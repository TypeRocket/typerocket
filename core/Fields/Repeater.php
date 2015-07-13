<?php
namespace TypeRocket\Fields;

use TypeRocket\Html\Generator as Generator,
    TypeRocket\Config as Config,
    TypeRocket\Utility as Utility;

class Repeater extends Field {

    public function __construct()
    {
        $paths = Config::getPaths();
        wp_enqueue_script( 'typerocket-booyah', $paths['urls']['assets'] . '/js/booyah.js', array( 'jquery' ), '1.0',
            true );
        wp_enqueue_script( 'jquery-ui-sortable', array( 'jquery' ), '1.0', true );
    }

    public function getString()
    {


        $this->setDebugStatus( false );

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
        if ($this->getDebugStatus() === true) {
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

        $this->setDebugStatus( null );

        return $this;
    }

}

