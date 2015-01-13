<?php
class tr_form extends tr_base {

  public $id = null;
  public $settings = array();
  public $controller = 'post';
  public $action = 'update';
  public $item_id = null;
  public $create_defaults = array();
  public $create_statics = array();
  public $current_field = '';
  public $get_values = true;
  public $group = null;
  public $sub = null;
  public $debug = null;
  private $hash = null;

  function __construct() {
    wp_enqueue_script( 'typerocket-scripts', tr::$paths['urls']['assets'] . '/js/typerocket.js', array('jquery'), '1', true );
  }

  public function make($controller = 'auto', $action = 'update', $item_id = null) {

    $this->auto_controller($controller, $item_id);

    $this->controller = $controller;
    $this->action = $action;
    $this->item_id = $item_id;
    $this->hash = wp_hash('_from_hash'.TR_SEED);

    do_action('tr_make_form', $this);

    return $this;
  }

  public function auto_controller(&$controller, &$item_id) {
    if($controller === 'auto') {
      global $post, $comment, $user_id;

      if(isset($post->ID) && is_null($item_id)) {
        $item_id = $post->ID;
        $controller = 'post';
      }
      elseif(isset($comment->comment_ID) && is_null($item_id)) {
        $item_id = $comment->comment_ID;
        $controller = 'comment';
      }
      elseif(isset($user_id) && is_null($item_id)) {
        $item_id = $user_id;
        $controller = 'user';
      }
      else {
        $controller = 'option';
      }
    }
  }

  public function set_settings($settings = array()) {
    $this->settings = $settings;
  }

  public function _e($v) {
    echo $v;
  }

  public function open($attr = array()) {

    $defaults = array(
      'action' => esc_attr($_SERVER["REQUEST_URI"]),
      'method' => 'post'
    );

    $attr = array_merge($defaults, $attr);

    $r = tr_html::open_element('form', $attr) . PHP_EOL;
    $r .= (isset($this->id)) ? tr_html::input('hidden', '_tr_form_id', $this->id) : '';
    $r .= wp_nonce_field($this->hash, '_tr_nonce_form', true, false);

    $this->_e($r);

    return $this;
  }

  public function close($value = false) {
    $html = '';
    if(is_string($value)) {
      $html .= tr_html::input('submit', '_tr_submit_form', $value, array('class' => 'button button-primary'));
    }

    $html .= '</form>';
    $this->_e($html);

    return $this;
  }

  public function process($action = null, $flash = true, $messages = array()) {
    if(is_null($action)) {
      $action = $this->action;
    }

    $message = null;
    $messages = array_merge($messages, array('update' => 'Data Updated', 'create' => 'Item Created', 'delete' => 'Item Deleted'));

    if(
      isset($_POST['_tr_nonce_form']) &&
      check_admin_referer($this->hash, '_tr_nonce_form')
    ) :
      switch($action) {
        case 'update' :
          $this->update();
          $message = $messages['update'];
          break;
        case 'create' :
          $this->create();
          $message = $messages['create'];
          break;
        case 'delete' :
          $this->delete();
          $message = $messages['delete'];
          break;
        default :
          die('From action is wrong.');
          break;
      }
    endif;

    if($flash == true && !get_transient( 'tr_flash_messages')) {
      set_transient( 'tr_flash_messages', $message);
    }

    return $this;
  }

  public function flash($message = null, $before = '<div class="updated tr-flash-message"><p>', $after = '</p></div>') {
    $tr_flash_messages = get_transient( 'tr_flash_messages');

    if(is_null($message)) {
      $message = $tr_flash_messages;
    }

    if($tr_flash_messages) {
      $this->_e($before.$message.$after);
      delete_transient( 'tr_flash_messages');
    }
  }

  private function update() {

    $crud = new tr_crud();

    switch($this->controller) {
      case 'post' :
        $crud->save_post( $this->item_id, 'update', $this );
        break;
      case 'user' :
        $crud->save_user( $this->item_id, 'update', $this );
        break;
      case 'comment' :
        $crud->save_comment( $this->item_id, 'update', $this );
        break;
      case 'option' :
        $crud->save_option( $this->item_id, 'update', $this );
        break;
      default :
        $crud->save_data( $this->controller, 'update', $this->item_id, $this );
        break;
    }

    return $this;
  }

  private function create() {

    $crud = new tr_crud();

    switch($this->controller) {
      case 'post' :
        $crud->save_post( null, 'create', $this );
        break;
      case 'user' :
        break;
      default :
        $crud->save_data( $this->controller, 'create', $this->item_id, $this );
        break;
    }

    return $this;
  }

  private function delete() {

    $crud = new tr_crud();

    switch($this->controller) {
      default :
        $crud->delete_data( $this->controller, 'delete', $this->item_id, $this );
        break;
    }

    return $this;
  }

  public function __call($name, $arguments) {
    if(!method_exists($this, $name)) {
      die('Form does not have this method: ' . $name);
    }
  }

  private function setup_field(&$field_obj, $name, &$settings) {

    do_action('tr_start_setup_field', $this, $field_obj, $name, $settings);

    if(is_string($this->group) && empty($settings['group'])) {
      $settings['group'] = $this->group;
    }

    if(is_string($this->sub) && empty($settings['sub'])) {
      $settings['sub'] = $this->sub;
    }

    if(isset($settings['builtin']) && $settings['builtin'] == true) {
      $field_obj->builtin = true;
    }

    $field_obj->connect($this);
    $field_obj->setup($name, $settings['group'], $settings['sub']);
    if(!isset($settings['label'])) {
      $settings['label'] = $name;
    }

    if($settings['template'] == true) {
      $field_obj->attr['data-name'] = $field_obj->attr['name'];
      unset($field_obj->attr['name']);
      unset($field_obj->attr['id']);
    }

    do_action('tr_end_setup_field', $this, $field_obj, $name, $settings);

  }

  public function add_field(&$field_obj, $settings = array(), $label = true) {
    $this->current_field = $field_obj;
    $this->current_field->settings = $settings;
    $this->current_field->label = $label;
    $field = $this->current_field->render();
    $label = $this->label();

    if(isset($this->current_field->settings['help'])) {
      $help = $this->current_field->settings['help'];
      $help =
        "<div class=\"help\">
          <p>{$help}</p>
        </div>";
    } else {
      $help = '';
    }

    if(empty($this->current_field->settings['html']) && $this->current_field->settings['html'] === false) {
      $html = $field;
    } else {
      $html =
      "<div class=\"control-section\">
        {$label}
        <div class=\"control\">
          {$field}{$help}
        </div>
      </div>";
    }
    $this->_e($html);
    $this->current_field = null;
  }

  private function label() {
    $open_html = "<div class=\"control-label\"><span class=\"label\">";
    $close_html = '</span></div>';
    $debug = $this->debug();

    if($this->current_field->label !== false) {
      $label = $this->current_field->settings['label'];
      $html = "{$open_html}{$label} {$debug}{$close_html}";
    }
    elseif($debug !== '') {
      $html = "{$open_html}{$debug}{$close_html}";
    }

    return $html;
  }

  private function is_debug() {
    return ($this->debug === false) ? $this->debug : TR_DEBUG;
  }

  private function debug() {
    $html = '';
    if($this->is_debug() === true && $this->current_field->builtin == false && is_admin() && $this->current_field->debuggable == true) {
      $html =
      "<div class=\"dev\">
        <span class=\"debug\"><i class=\"tr-icon-bug\"></i></span>
          <span class=\"nav\">
          <span class=\"field\">
            <i class=\"tr-icon-code\"></i><span>tr_{$this->controller}_field(\"{$this->current_field->brackets}\");</span>
          </span>
        </span>
      </div>";
    }
    return $html;
  }

  public function division($headline = 'Division', $description = null) {
    $content ="<div class=\"control-division\">";

    if(is_string($headline)) {
      $headline = force_balance_tags($headline);
      $content .= "<h2>{$headline}</h2>";
    }

    if(is_string($description)) {
      $description = force_balance_tags($description);
      $content .= "<p>{$description}</p>";
    }

    $content .= "</div>";

    $content = apply_filters('tr_from_division', $content, $headline, $description);

    $this->_e($content);

    return $this;
  }

  public function repeater($name, $fields, $settings = array(), $label = 'Repeater' ) {
    wp_enqueue_script( 'typerocket-booyah', tr::$paths['urls']['assets'] . '/js/booyah.js', array('jquery'), '1.0', true );
    wp_enqueue_script('jquery-ui-sortable', array( 'jquery' ), '1.0', true);

    $this->debug = false;

    // add controls
    if(isset($settings['help'])) {
      $help =
        "<div class=\"help\">
          <p>{$settings['help']}</p>
        </div>";
    } else {
      $help = '';
    }

    // add buttom settings
    if(isset($settings['add_button'])) {
      $add_button_value = $settings['add_button'];
    } else {
      $add_button_value = "Add New";
    }

    // add label
    if(is_string($label)) {
      $label = "<div class=\"control-label\"><span class=\"label\">{$label}</span></div>";
    }

    // template for repeater groups
    $templatesContainer = '<div class="repeater-controls"><div class="collapse tr-icon-arrow-up"></div><div class="move tr-icon-menu"></div><a href="#remove" class="remove tr-icon-remove2" title="remove"></a></div><div class="repeater-inputs">';
    $templatesContainerEnd = '</div></div>';

    $this->_e('<div class="control-section tr-repeater">'); // start tr-repeater

    // setup repeater
    $cache_group = $this->group;
    $cache_sub = $this->sub;
    $this->sanitize_string($name);
    $root_group = $this->group .=  "[{$name}]";
    $this->group .= "[{{ {$name} }}]";

    // debug
    $debug = '';
    if(TR_DEBUG === true && is_admin()) {
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

    $this->_e($debug);

    $this->_e($label);

    // add controls (add, flip, clear all)
    $this->_e("<div class=\"controls\"><div class=\"tr-repeater-button-add\"><input type=\"button\" value=\"{$add_button_value}\" class=\"button add\" /></div><div class=\"button-group\"><input type=\"button\" value=\"Flip\" class=\"flip button\" /><input type=\"button\" value=\"Collapse\" class=\"tr_action_collapse button\"><input type=\"button\" value=\"Clear All\" class=\"clear button\" /></div>{$help}</div>");

    // render js template data
    $this->_e('<div class="tr-repeater-group-template" data-id="'.$name.'">');
    $this->_e($templatesContainer);
    $this->render_fields($fields, 'template');
    $this->_e($templatesContainerEnd);

    // render saved data
    $this->_e('<div class="tr-repeater-fields">'); // start tr-repeater-fields
    $getter = new tr_get_field();
    $repeats = $getter->value($root_group, $this->item_id, $this->controller);
    if(is_array($repeats)) {
      foreach($repeats as $k => $array) {
        $this->_e('<div class="tr-repeater-group">');
        $this->_e($templatesContainer);
        $this->group = $root_group . "[{$k}]";
        $this->render_fields($fields);
        $this->_e($templatesContainerEnd);
      }
    }
    $this->_e('</div>'); // end tr-repeater-fields
    $this->group = $cache_group;
    $this->sub = $cache_sub;
    $this->_e('</div>'); // end tr-repeater

    $this->debug = null;

  }

  public function render_fields($fields = array(), $type = null ) {
    foreach($fields as $args) {

      if( empty($args[1][1]) ) { $args[1][1] = array(); }
      if( empty($args[1][2]) ) { $args[1][2] = array(); }

      if( $args[0] == 'select' || $args[0] == 'radio' || $args[0] == 'custom') {
        if( empty($args[1][3]) ) { $args[1][3] = array(); }
        if( is_string($type) ) { $args[1][3][$type] = true; }
        call_user_func_array(array($this, $args[0]), $args[1]);
      } else {
        if( is_string($type) ) { $args[1][2][$type] = true; }
        call_user_func_array(array($this, $args[0]), $args[1]);
      }

    }
  }

  public function text($name, $attr = array(), $settings = array(), $label = true) {
    $field = new tr_field_text();
    $field->connect($this);
    $this->setup_field($field, $name, $settings);
    $field->attr += $attr;
    $this->add_field($field, $settings, $label);

    return $this;
  }

  public function email($name, $attr = array(), $settings = array(), $label = true) {
    $field = new tr_field_text();
    $this->setup_field($field, $name, $settings);
    $field->attr += $attr;
    $field->type = 'email';
    $this->add_field($field, $settings, $label);

    return $this;
  }

  public function number($name, $attr = array(), $settings = array(), $label = true) {
    $field = new tr_field_text();
    $this->setup_field($field, $name, $settings);
    $field->attr += $attr;
    $field->type = 'number';
    $this->add_field($field, $settings, $label);

    return $this;
  }

  public function password($name, $attr = array(), $settings = array(), $label = true) {
    $field = new tr_field_text();
    $this->setup_field($field, $name, $settings);
    $field->attr += $attr;
    $field->type = 'password';
    $field->attr['autocomplete'] = 'off';
    $this->add_field($field, $settings, $label);

    return $this;
  }

  public function hidden($name, $attr = array(), $settings = array(), $label = false) {
    $field = new tr_field_text();
    $this->setup_field($field, $name, $settings);
    $field->attr += $attr;
    $field->type = 'hidden';
    $settings['html'] = false;
    $this->add_field($field, $settings, $label);

    return $this;
  }

  public function submit($name, $attr = array(), $settings = array(), $label = false) {
    $field = new tr_field_submit();
    $this->setup_field($field, $name, $settings);
    $field->attr['value'] = $name;
    $field->attr += $attr;
    $this->add_field($field, $settings, $label);

    return $this;
  }

  public function textarea($name, $attr = array(), $settings = array(), $label = true) {
    $field = new tr_field_textarea();
    $this->setup_field($field, $name, $settings);
    $field->attr += $attr;
    $this->add_field($field, $settings, $label);

    return $this;
  }

  public function radio($name, $options, $attr = array(), $settings = array(), $label = true) {
    $field = new tr_field_radio();
    $this->setup_field($field, $name, $settings);
    $field->attr += $attr;
    $field->options = $options;
    $this->add_field($field, $settings, $label);

    return $this;
  }

  public function checkbox($name, $attr = array(), $settings = array(), $label = true) {
    $field = new tr_field_checkbox();
    $this->setup_field($field, $name, $settings);
    $field->attr += $attr;
    $this->add_field($field, $settings, $label);

    return $this;
  }

  public function select($name, $options, $attr = array(), $settings = array(), $label = true) {
    $field = new tr_field_select();
    $this->setup_field($field, $name, $settings);
    $field->attr += $attr;
    $field->options = $options;
    $this->add_field($field, $settings, $label);

    return $this;
  }

  public function editor($name, $options = array(), $attr = array(), $settings = array(), $label = true) {
    $field = new tr_field_editor();
    $this->setup_field($field, $name, $settings);
    $field->attr += $attr;
    $field->options = $options;
    $this->add_field($field, $settings, $label);

    return $this;
  }

  public function color($name, $attr = array(), $settings = array(), $label = true) {
    $field = new tr_field_color();
    $this->setup_field($field, $name, $settings);
    $field->attr += $attr;
    $this->add_field($field, $settings, $label);

    return $this;
  }

  public function date($name, $attr = array(), $settings = array(), $label = true) {
    $field = new tr_field_date();
    $this->setup_field($field, $name, $settings);
    $field->attr += $attr;
    $this->add_field($field, $settings, $label);

    return $this;
  }

  public function time($name, $attr = array(), $settings = array(), $label = true) {
    $field = new tr_field_time();
    $this->setup_field($field, $name, $settings);
    $field->attr += $attr;
    $this->add_field($field, $settings, $label);

    return $this;
  }

  public function image($name, $attr = array(), $settings = array(), $label = true) {
    $field = new tr_field_image();
    $this->setup_field($field, $name, $settings);
    $field->attr += $attr;
    $this->add_field($field, $settings, $label);

    return $this;
  }

  public function file($name, $attr = array(), $settings = array(), $label = true) {
    $field = new tr_field_file();
    $this->setup_field($field, $name, $settings);
    $field->attr += $attr;
    $this->add_field($field, $settings, $label);

    return $this;
  }

  public function gallery($name, $attr = array(), $settings = array(), $label = true) {
    $field = new tr_field_gallery();
    $this->setup_field($field, $name, $settings);
    $field->attr += $attr;
    $this->add_field($field, $settings, $label);

    return $this;
  }

  public function items($name, $attr = array(), $settings = array(), $label = true) {
    $field = new tr_field_items();
    $this->setup_field($field, $name, $settings);
    $field->attr += $attr;
    $this->add_field($field, $settings, $label);

    return $this;
  }

  public function custom(&$field, $name, $attr = array(), $settings = array(), $label = true) {
    $this->setup_field($field, $name, $settings);
    $field->attr += $attr;
    $this->add_field($field, $settings, $label);

    return $this;
  }

}
