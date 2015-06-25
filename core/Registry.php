<?php
namespace TypeRocket;

class Registry {
  public static $obj = array();

  public static function add(&$obj = null) {
    if(is_object($obj)) {
      self::merge(self::$obj, array($obj));
    }
  }

  public static function run() {
    foreach(self::$obj as $v) :
      if( $v instanceof tr_taxonomy ) {
        add_action('init', array($v, 'bake'));

        // $taxonomy . '_edit_form'
        // $taxonomy . '_add_form_fields'
        if( isset($v->form['bottom']) && $v->form['bottom'] == true ) {
          add_action( $v->singular . '_edit_form', array($v, 'edit_form_bottom'));
          add_action( $v->singular . '_add_form_fields', array($v, 'add_form_bottom'));
        }

      }
      elseif( $v instanceof tr_post_type ) {
        add_action('init', array($v, 'bake'));

        if( is_string($v->title) ) {
          add_filter('enter_title_here', array($v, 'enter_title_here'));
        }

        // edit_form_top
        if( isset($v->form['top']) && $v->form['top'] == true ) {
          add_action('edit_form_top', array($v, 'edit_form_top'));
        }

        // edit_form_after_title
        if( isset($v->form['title']) && $v->form['title'] == true  ) {
          add_action('edit_form_after_title', array($v, 'edit_form_after_title'));
        }

        // edit_form_after_editor
        if( isset($v->form['editor']) && $v->form['editor'] == true  ) {
          add_action('edit_form_after_editor', array($v, 'edit_form_after_editor'));
        }

        // dbx_post_sidebar
        if( isset($v->form['bottom']) && $v->form['bottom'] == true  ) {
          add_action('dbx_post_sidebar', array($v, 'dbx_post_sidebar'));
        }

      }
      elseif( $v instanceof tr_meta_box ) {
        add_action( 'add_meta_boxes', array($v, 'bake') );
      }

    endforeach;
  }
}