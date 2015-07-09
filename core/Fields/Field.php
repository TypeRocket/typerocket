<?php
namespace TypeRocket\Fields;

abstract class Field
{

    public $id = null;
    public $name = null;
    public $group = '';
    public $type = '';
    public $sub = '';
    public $prefix = 'tr';
    public $attr = array();
    public $brackets = '[]';
    /** @var \TypeRocket\Form $form */
    public $label = false;
    public $settings = false;
    public $options = null;
    public $repeatable = true;
    public $builtin = false;
    public $debuggable = true;

    public function connect_form( $form )
    {
        if ($form instanceof \TypeRocket\Form) {
            $this->setGroup( $form->getGroup() );
            $this->setSub( $form->getSub() );
        }

        return $this;
    }

    public function setGroup($group) {
        if (\TypeRocket\Validate::bracket( $group )) {
            $this->group = $group;
        }

        return $this;
    }

    public function setSub($sub) {
        if (\TypeRocket\Validate::bracket( $sub )) {
            $this->sub = $sub;
        }

        return $this;
    }

    public function setName( $name, $group = '', $sub = '' )
    {
        $utility = new \TypeRocket\Utility();
        $utility->sanitize_string( $name );

        if ($this->builtin == true) {
            $this->prefix = '_tr_builtin_data';
        }

        $this->name         = $name;
        $this->group        = $group;
        $this->sub          = $sub;
        $this->brackets     = $this->get_field_bracket();
        $this->attr['name'] = $this->prefix . $this->brackets;

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
    public function setup( $name, $settings, array $attr = array(), $label = true)
    {

        do_action( 'tr_start_setup_field', $this, $name, $settings );

        $this->settings = $settings;
        $this->label = $label;

        if (is_string( $this->form->getGroup() ) && empty( $settings['group'] )) {
            $settings['group'] = $this->form->getGroup();
        }

        if (is_string( $this->form->sub ) && empty( $settings['sub'] )) {
            $settings['sub'] = $this->form->sub;
        }

        if (isset( $settings['builtin'] ) && $settings['builtin'] == true) {
            $this->builtin = true;
        }

        if (array_key_exists( 'class', $attr )) {
            $attr['class'] .= ' ' . $this->attr['class'];
        }

        $this->attr = array_merge( $this->attr, $attr );

        $this->setName( $name, $settings['group'], $settings['sub'] );

        $html_class = trim( $this->attr['class'] . ' ' . $this->prefix . '_field_class_' . $this->name );

        $this->attr['class'] = apply_filters( 'tr_field_html_class_filter', $html_class, $this );

        if ( ! isset( $settings['label'] ) ) {
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

        if ($this->form->get_values == false) {
            return null;
        }

        $getter = new \TypeRocket\GetField();

        return $getter->value_from_field_obj( $this );
    }

    public function get_field_bracket()
    {
        return "{$this->group}[{$this->name}]{$this->sub}";
    }

    public function render()
    {
        return '';
    }

}