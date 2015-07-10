<?php
namespace TypeRocket\Fields;

use \TypeRocket\Form as Form,
    \TypeRocket\Validate as Validate,
    \TypeRocket\Utility as Utility,
    \TypeRocket\GetField as GetField;

abstract class Field
{

    // Element attributes
    public $id = null;
    public $name = null;
    public $type = null;
    public $attr = array();

    // controller settings
    private $item_id = null;
    private $controller = null;
    public $group = null;

    public $sub = null;
    public $prefix = null;
    public $brackets = null;

    public $label = false;
    public $settings = false;
    public $options = null;
    public $builtin = false;
    public $repeatable = true;
    public $fillable = true;
    public $debuggable = true;

    public function connectForm( $form )
    {
        if ($form instanceof Form) {
            $this->setGroup( $form->getGroup() );
            $this->setSub( $form->getSub() );
            $this->setItemId( $form->getItemId() );
            $this->setController( $form->getController() );
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

    public function setSub( $sub )
    {
        if (Validate::bracket( $sub )) {
            $this->sub = $sub;
        }

        return $this;
    }

    public function setItemId( $item_id )
    {
        if (isset( $item_id )) {
            $this->item_id = (int) $item_id;
        }

        return $this;
    }

    public function getItemId()
    {
        return $this->item_id;
    }

    public function setController( $controller )
    {
        if (isset( $controller )) {
            $this->controller = $controller;
        }

        return $this;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setName( $name )
    {
        $utility = new Utility();
        $utility->sanitize_string( $name );
        $this->name = $name;

        return $this;
    }

    public function setPrefix( $prefix )
    {

        $this->prefix = $prefix;

        if ($this->builtin == true) {
            $this->prefix = '_tr_builtin_data';
        }

        return $this;
    }

    /**
     * @param $name
     * @param $settings
     * @param array $attr
     *
     * @param bool $label
     *
     * @return $this
     */
    public function setup( $name, $settings, array $attr = array(), $label = true )
    {

        do_action( 'tr_start_setup_field', $this, $name, $settings );

        $this->settings = $settings;
        $this->label    = $label;


        if (isset( $settings['builtin'] ) && $settings['builtin'] == true) {
            $this->builtin = true;
        }

        if (array_key_exists( 'class', $attr )) {
            $attr['class'] .= ' ' . $this->attr['class'];
        }

        $this->attr = array_merge( $this->attr, $attr );

        $this->setPrefix( 'tr' )->setName( $name )->setBrackets( $this->getBrackets() );

        $this->attr['name'] = $this->prefix . $this->brackets;

        $html_class = trim( $this->attr['class'] . ' ' . $this->prefix . '_field_class_' . $this->name );

        $this->attr['class'] = apply_filters( 'tr_field_html_class_filter', $html_class, $this );

        if ( ! isset( $settings['label'] )) {
            $this->settings['label'] = $name;
        }

        if ($settings['template'] == true) {
            $this->attr['data-name'] = $this->attr['name'];
            unset( $this->attr['name'] );
            unset( $this->attr['id'] );
        }

        do_action( 'tr_end_setup_field', $this, $name, $settings );

        return $this;

    }

    public function getValue()
    {

        if ($this->fillable == false) {
            return null;
        }

        $getter = new GetField();

        return $getter->getFieldValue( $this );
    }

    public function getBrackets()
    {
        return "{$this->group}[{$this->name}]{$this->sub}";
    }

    public function setBrackets( $brackets )
    {
        $this->brackets = $brackets;

        return $this;
    }

    public function render()
    {
        return '';
    }

}