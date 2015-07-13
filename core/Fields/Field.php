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

    // form settings
    private $item_id = null;
    private $controller = null;
    /** @var Form */
    private $form = null;

    // used to build the attribute name
    private $prefix = null;
    private $group = null;
    private $sub = null;
    private $brackets = null;

    private $label = false;
    private $settings = array();
    private $builtin = false;
    private $populate = true;

    public function __get( $property )
    {
    }

    public function __set( $property, $value )
    {
    }

    public function setupByForm( Form $form )
    {
        $this->setGroup( $form->getGroup() );
        $this->setSub( $form->getSub() );
        $this->setItemId( $form->getItemId() );
        $this->setController( $form->getController() );
        $this->setPopulate( $form->getPopulate() );
        $this->setForm( clone $form ); // do not pass by reference

        return $this;
    }

    public function setForm(Form $form) {
        $this->form = $form;

        return $this;
    }

    public function getForm()
    {
        return $this->form;
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

    public function setPopulate($populate) {
        $this->populate = (bool) $populate;

        return $this;
    }

    function getPopulate() {
        return $this->populate;
    }

    public function setBuiltin($in) {
        $this->builtin = (bool) $in;

        return $this;
    }

    function getBuiltin() {
        return $this->builtin;
    }

    public function setLabel($label) {
        $this->label = (bool) $label;

        return $this;
    }

    function getLabel() {
        return $this->label;
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

    public function removeSetting($key) {

        if(array_key_exists($key, $this->settings)) {
            unset($this->settings[$key]);
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

    public function getName()
    {
        return $this->name;
    }

    public function setSetting( $key, $value )
    {
        $this->settings[$key] = $value;

        return $this;
    }

    public function getSetting($key) {

        if( ! array_key_exists($key, $this->settings)) {
            return null;
        }

        return $this->settings[$key];
    }

    public function setPrefix( $prefix )
    {

        $this->prefix = (string) $prefix;

        if ($this->builtin == true) {
            $this->prefix = '_tr_builtin_data';
        }

        return $this;
    }

    public function appendStringToAttribute($key, $text) {

        if(array_key_exists($key, $this->attr)) {
            $text .= $this->attr[$key] . (string) $text;
        }

        $this->attr[$key] = $text;

        return $this;
    }

    public function getPrefix() {
        return $this->prefix;
    }

    /**
     * @param $name
     * @param array $attr
     * @param array $settings
     *
     * @param bool $label
     *
     * @return $this
     */
    public function setup( $name, array $attr = array(), array $settings = array(), $label = true )
    {

        do_action( 'tr_start_setup_field', $this, $name, $attr, $settings, $label );

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

        do_action( 'tr_end_setup_field', $this, $name, $attr, $settings, $label);

        return $this;

    }

    public function getValue()
    {

        if ($this->populate == false) {
            return null;
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

    public function getString()
    {
        return '';
    }

}