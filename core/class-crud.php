<?php

class tr_crud extends tr_base {

  public $controller = null;
  public $action = null;
  public $item_id = null;
  public $form_obj = null;
  public $fields = null;

   function reset() {
    $this->controller = $this->action = $this->item_id = $this->form_obj = $this->fields = null;
  }

   function save_post($post_id, $action = 'update', $form_obj = null) {

    $this->controller = 'post';
    $this->action = $action;
    $this->item_id = $post_id;
    $this->form_obj = $form_obj;

    $this->controller_switch();
    $this->reset();
  }

   function save_user($user_id, $action = 'update', $form_obj = null) {
    if ( current_user_can('edit_user', $user_id) ) {
      $this->controller = 'user';
      $this->action = $action;
      $this->item_id = $user_id;
      $this->form_obj = $form_obj;

      $this->controller_switch();
      $this->reset();
    }
  }

   function save_comment($comment_id, $action = 'update', $form_obj = null) {

    $this->controller = 'comment';
    $this->action = $action;
    $this->item_id = $comment_id;
    $this->form_obj = $form_obj;

    $this->controller_switch();
    $this->reset();
  }

   function save_option($action = 'update', $form_obj = null) {

    $this->controller = 'option';
    $this->action = $action;
    $this->item_id = null;
    $this->form_obj = $form_obj;

    $this->controller_switch();
    $this->reset();
  }

   function save_data($controller, $action = 'update', $item_id = null, $form_obj = null) {

    $this->controller = $controller;
    $this->action = $action;
    $this->item_id = $item_id;
    $this->form_obj = $form_obj;

    $this->controller_switch();
    $this->reset();
  }

  function delete_data($controller, $action = 'delete', $item_id = null, $form_obj = null) {

    $this->controller = $controller;
    $this->action = $action;
    $this->item_id = $item_id;
    $this->form_obj = $form_obj;

    $this->controller_switch();
    $this->reset();
  }

   function controller_switch() {

    do_action('tr_start_save', $_POST);
    $the_post_vars = apply_filters('tr_save_filter', $_POST);
    $this->fields = $the_post_vars['tr'];
    // TODO: data validation should go here when it is built
    /* We need to be able to detect the form and make sure no extra fields have been added. Users also need to be able to check their data in an easy way. */

    switch($this->controller) {
      case 'post' :
        $this->post_action_switch();
        break;
      case 'user' :
        $this->user_action_switch();
        break;
      case 'comment' :
        $this->comment_action_switch();
        break;
      case 'option' :
        $this->option_action_switch();
        break;
      default :
        $func = 'tr_controller_switch_' . $this->controller;
        call_user_func($func, $this);
        break;
    }

    do_action('tr_end_save', $_POST);
  }

  private function post_action_switch() {
    if(isset($_POST['_tr_builtin_data']) && $this->action == 'update') :
      remove_action('save_post', array($this, 'save_post'));
      $_POST['_tr_builtin_data']['ID'] = $this->item_id;
      wp_update_post( $_POST['_tr_builtin_data'] );
      add_action('save_post', array($this, 'save_post'));
    elseif( $this->action == 'create' ) :
      remove_action('save_post', array($this, 'save_post'));
      $insert = array_merge(
        $this->form_obj->create_defaults,
        $_POST['_tr_builtin_data'],
        $this->form_obj->create_s
      );
      $this->item_id = wp_insert_post( $insert );
      add_action('save_post', array($this, 'save_post'));
    endif;
    $this->post_meta_actions();
  }

  private  function user_action_switch() {
    if(isset($_POST['_tr_builtin_data']) && $this->action == 'update') :
      $_POST['_tr_builtin_data']['ID'] = $this->item_id;
      wp_update_user( $_POST['_tr_builtin_data'] );
      unset($this->fields['user_insert']);
    elseif( $this->action == 'create' ) :
      $insert = array_merge(
        $this->form_obj->create_defaults,
        $_POST['_tr_builtin_data'],
        $this->form_obj->create_s
      );
      $this->item_id = wp_insert_user( $insert );
    endif;

    $this->user_meta_actions();
  }

  private  function comment_action_switch() {
    $this->comment_meta_actions();
  }

  private  function option_action_switch() {
    $this->option_actions();
  }

  private  function post_meta_actions() {

    if(is_array($this->fields)) :
      if($parent_id = wp_is_post_revision($this->item_id)) {
        $this->item_id = $parent_id;
      }

      foreach($this->fields as $key => $value) :
        if(is_string($value)) {
          $value = trim($value);
        }

        $current_value = get_post_meta($this->item_id, $key, true);

        if (isset($value) && $value !== $current_value ) :
          update_post_meta($this->item_id, $key, $value);
        elseif( empty($value) && $value !== 0 ) :
          delete_post_meta($this->item_id, $key);
        endif;

      endforeach;
    endif;
  }

  private  function comment_meta_actions() {

    if(is_array($this->fields)) :
      foreach($this->fields as $key => $value) :
        if(is_string($value)) {
          $value = trim($value);
        }

        $current_value = get_comment_meta($this->item_id, $key, true);

        if (isset($value) && $value !== $current_value ) :
          update_comment_meta($this->item_id, $key, $value);
        elseif( empty($value) && $value !== 0 ) :
          delete_comment_meta($this->item_id, $key);
        endif;

      endforeach;
    endif;
  }

  private  function option_actions() {
    if(is_array($this->fields)) :
    foreach($this->fields as $key => $value) :

      if(is_string($value)) $value = trim($value);

      $current_meta = get_option($key);

      if ( isset($value) && $current_meta !== $value ) :
        update_option( $key, $value );
      elseif( empty($value) && isset($current_meta)) :
        delete_option( $key );
      endif;

    endforeach;
    endif;
  }

  private  function user_meta_actions() {

    if(is_array($this->fields)) :
    foreach($this->fields as $key => $value) :
      if(is_string($value)) {
        $value = trim($value);
      }

      $current_value = get_user_meta($this->item_id, $key, true);

      if (isset($value) && $value !== $current_value ) :
        update_user_meta($this->item_id, $key, $value);
      elseif( empty($value) && $value !== 0 ) :
        delete_user_meta($this->item_id, $key);
      endif;

    endforeach;
    endif;
  }

  public function get_serialized_fields($fields = null) {

    $fields = (is_array($fields)) ? $fields : $this->fields;

    if(is_array($fields)) {
      foreach($fields as $k => $v) {
        $fields[$k] = maybe_serialize($v);
      }
    }

    return $fields;

  }

  public function get_field_formats() {

    $formats = array();

    if(is_array($this->fields)) {
      foreach($this->fields as $v) {
        $formats []= '%s';
      }
    }

    return $formats;
  }

  public function activate_db() {
    global $wpdb;

    $r = null;

    $table = $wpdb->prefix.$this->controller;
    $fields = $this->get_serialized_fields();
    $field_formats = $this->get_field_formats();

    switch($this->action) {
      case 'update' :
        $r = $wpdb->update(
          $table,
          $fields,
          array( 'id' => $this->item_id ),
          $field_formats,
          array( '%d' )
        );
        break;
      case 'create' :
        $wpdb->insert(
          $table,
          $fields,
          $field_formats
        );
        $r = $wpdb->insert_id;
        break;
      case 'delete' :
        $r = $wpdb->delete( $table , array( 'id' => $this->item_id ), array( '%d' ) );
        break;
    }

    return $r;
  }

}