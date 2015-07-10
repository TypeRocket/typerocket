<?php
namespace TypeRocket\Fields;

use \TypeRocket\Form as Form,
    \TypeRocket\Validate as Validate,
    \TypeRocket\Utility as Utility,
    \TypeRocket\GetValue as GetValue;

abstract class Field
{

    // Element attributes
    private $name = null;
    private $type = null;
    private $attr = array();

    // controller settings
    private $item_id = null;
    private $controller = null;

    // used to build the attribute name
    private $prefix = null;
    private $group = null;
    private $sub = null;
    private $brackets = null;

    public $label = false;
    public $settings = false;
    public $builtin = false;
    public $populate = true;

    public function setupByForm( $form )
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

    public function setAttributes( array $attributes) {
        $this->attr = $attributes;

        return $this;
    }

    public function getAttributes() {
        return $this->attr;
    }

    public function getAttribute( $key )
    {
        if( ! array_key_exists($key, $this->attr)) {
            return null;
        }

        return $this->attr[$key];
    }

    public function setAttribute( $key, $value )
    {
        $this->attr[ (string) $key] = $value;

        return $this;
    }

    public function removeAttribute($key) {

        if(array_key_exists($key, $this->attr)) {
            unset($this->attr[$key]);
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

    public function setType($type) {

        $this->type = (string) $type;

        return $this;
    }

    public function getType() {
        return $this->type;
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
    public function setup( $name, array $settings = array(), array $attr = array(), $label = true )
    {

        do_action( 'tr_start_setup_field', $this, $name, $settings, $attr, $label );

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

        do_action( 'tr_end_setup_field', $this, $name, $settings, $attr, $label);

        return $this;

    }

    public function getValue()
    {

        if ($this->populate == false) {
            return false;
        }

        $getter = new GetValue();

        return $getter->getFromField( $this );
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