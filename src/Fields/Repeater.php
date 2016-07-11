<?php
namespace TypeRocket\Fields;

use TypeRocket\Html\Generator;

class Repeater extends Field implements ScriptField
{

    private $fields = [];
    private $headline = null;

    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'repeater' );
    }

    public function enqueueScripts()
    {
        wp_enqueue_script( 'jquery-ui-sortable', ['jquery'], '1.0', true );
    }

    /**
     * Covert Repeater to HTML string
     */
    public function getString()
    {
        $this->setAttribute( 'name', $this->getNameAttributeString() );
        $form = $this->getForm();
        $settings = $this->getSettings();
        $name     = $this->getName();
        $form->setDebugStatus( false );
        $html     = '';

        $headline = $this->headline ? '<h1>' . $this->headline . '</h1>': '';

        // add controls
        if (isset( $settings['help'] )) {
            $help = "<div class=\"help\"> <p>{$settings['help']}</p> </div>";
            $this->removeSetting( 'help' );
        } else {
            $help = '';
        }

        // add button settings
        if (isset( $settings['add_button'] )) {
            $add_button_value = $settings['add_button'];
        } else {
            $add_button_value = "Add New";
        }

        // template for repeater groups
        $href          = '#remove';
        $openContainer = '<div class="repeater-controls"><div class="collapse"></div><div class="move"></div><a href="' . $href . '" class="remove" title="remove"></a></div><div class="repeater-inputs">';
        $endContainer  = '</div>';

        $html .= '<div class="control-section tr-repeater">'; // start tr-repeater

        // setup repeater
        $cache_group = $form->getGroup();
        $cache_sub   = $form->getSub();

        $root_group = $this->getDots();
        $form->setGroup( $this->getDots() . ".{{ {$name} }}" );

        // add controls (add, flip, clear all)
        $generator    = new Generator();
        $default_null = $generator->newInput( 'hidden', $this->getAttribute( 'name' ), null )->getString();

        $html .= "<div class=\"controls\"><div class=\"tr-repeater-button-add\"><input type=\"button\" value=\"{$add_button_value}\" class=\"button add\" /></div><div class=\"button-group\"><input type=\"button\" value=\"Flip\" class=\"flip button\" /><input type=\"button\" value=\"Contract\" class=\"tr_action_collapse button\"><input type=\"button\" value=\"Clear All\" class=\"clear button\" /></div>{$help}<div>{$default_null}</div></div>";

        // replace name attr with data-name so fields are not saved
        $templateFields = str_replace( ' name="', ' data-name="', $this->getTemplateFields() );

        // render js template data
        $html .= "<div class=\"tr-repeater-group-template\" data-id=\"{$name}\">";
        $html .= $openContainer . $headline . $templateFields . $endContainer;
        $html .= '</div>';

        // render saved data
        $html .= '<div class="tr-repeater-fields">'; // start tr-repeater-fields
        $repeats = $this->getValue();
        if ( is_array( $repeats ) ) {
            foreach ($repeats as $k => $array) {
                $html .= '<div class="tr-repeater-group">';
                $html .= $openContainer;
                $form->setGroup( $root_group . ".{$k}" );
                $html .= $headline;
                $html .= $form->getFromFieldsString( $this->fields );
                $html .= $endContainer;
                $html .= '</div>';
            }
        }
        $html .= '</div>'; // end tr-repeater-fields
        $form->setGroup( $cache_group );
        $form->setSub( $cache_sub );
        $html .= '</div>'; // end tr-repeater

        return $html;
    }

    private function getTemplateFields()
    {
        return $this->getForm()->setDebugStatus(false)->getFromFieldsString( $this->fields );
    }

    public function setFields( array $fields )
    {
        $this->fields = $fields;

        return $this;
    }

    public function appendField( $field )
    {
        if(is_array($field) || $field instanceof Field) {
            $this->fields[] = $field;
        }

        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setHeadline($headline = null) {
        $this->headline = $headline;

        return $this;
    }

    public function getHeadline()
    {
        return $this->headline;
    }

}

