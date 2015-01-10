<?php
class tr_field extends tr_base {

  public $id = null;
  public $name = null;
  public $group = '';
  public $type = '';
  public $sub = '';
  public $prefix = 'tr';
  public $attr = array();
  public $brackets = '[]';
  public $form = null;
  public $label = false;
  public $settings = false;
  public $options = null;
  public $repeatable = true;
  public $builtin = false;
  public $debuggable = true;

  public function connect(&$form_obj) {
    if( $form_obj instanceof tr_form ) {
      $this->form = $form_obj;
    }
  }

  public function setup($name, $group = '', $sub = '') {
    $this->check($name, 'Making Field: You need to enter a singular name.');
    $this->sanitize_string($name);

    if($this->builtin == true) {
      $this->prefix = '_tr_builtin_data';
    }

    $this->name = $name;
    $this->group = $group;
    $this->sub = $sub;
    $this->brackets = $this->get_field_bracket();
    $this->attr['name'] = $this->prefix . $this->brackets;
    $this->attr['class'] = $this->prefix . '_field_class_' . $this->name ;
  }

  public function get_value() {

    if($this->form->get_values == false) {
      return null;
    }

    $getter = new tr_get_field();
    return $getter->value_from_field_obj($this);
  }

  public function get_field_bracket() {
    return "{$this->group}[{$this->name}]{$this->sub}";
  }

}