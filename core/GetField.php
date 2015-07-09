<?php
namespace TypeRocket;

class GetField {

  function value($brackets, $item_id, $controller, $builtin = false) {
    $keys = $this->get_bracket_keys($brackets);
    $data = $this->controller_switch($keys[0], $item_id, $controller, $builtin);
    return $this->parse_value_data($data, $keys);
  }

  function value_from_field_obj($field_obj) {
    return $this->value($field_obj->brackets, $field_obj->form->item_id, $field_obj->form->controller, $field_obj->builtin);
  }

  function parse_value_data($data, $keys) {
    if(isset($keys[1]) && !empty($data)) {
      $data = maybe_unserialize($data);

      // unset first key since $data is already set to it
      unset($keys[0]);

      if(!empty($keys) && is_array($keys)) {
        foreach($keys as $name) {
          $data = isset($data[$name]) ? $data[$name] : false;
        }
      }

    }
	$utility = new Utility();
    $utility->unslash($data);
    return $data;
  }

  function controller_switch($the_field, $item_id, $controller, $builtin) {
    switch($controller) {
      case 'post' :
        if($builtin == true) {
          $data = get_post_field($the_field, $item_id, 'raw');
        } else {
          $data = get_metadata( 'post', $item_id, $the_field, true);
        }
        break;
      case 'user' :
        if($builtin == true) {
          $data = $this->get_user_data($item_id, $the_field);
        } else {
          $data = get_metadata( 'user', $item_id, $the_field, true);
        }
        break;
      case 'comment' :
        $data = get_metadata( 'comment', $item_id, $the_field, true);
        break;
      case 'option' :
        $data = get_option($the_field);
        break;
      default :
        $func = 'tr_get_data_' . $controller;
        $data = call_user_func($func, $controller, $item_id, $the_field);
        break;
    }

    $data = apply_filters('tr_field_data_filter', $data, $this, $the_field, $item_id, $controller, $builtin);

    return $data;
  }

  function get_bracket_keys($str, $set = 1) {
    $regex = '/\[([^]]+)\]/i';
    preg_match_all($regex, $str, $matches, PREG_PATTERN_ORDER);

    return $matches[$set];
  }

  function get_user_data($item_id, $the_field) {
    switch($the_field) {
      case 'user_login' :
      case 'user_nicename' :
      case 'user_email' :
      case 'user_url' :
      case 'display_name' :
      case 'user_registered' :
        $data = get_userdata($item_id);
        $data = $data->$the_field;
        break;
      case 'user_pass' :
        $data = '';
        break;
      default :
        $data = get_user_meta($item_id, $the_field, true);
        break;
    }

    return $data;

  }

}