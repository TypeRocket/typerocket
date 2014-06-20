<?php
/**
 * Class TypeRocket base class
 *
 * Base class for other classes to extend
 */
class tr_base {

  protected $reserved_names = array(
    'attachment' => true,
    'attachment_id' => true,
    'author' => true,
    'author_name' => true,
    'action' => true,
    'calendar' => true,
    'cat' => true,
    'category' => true,
    'category__and' => true,
    'category__in' => true,
    'category__not_in' => true,
    'category_name' => true,
    'comments_per_page' => true,
    'comments_popup' => true,
    'customize_messenger_channel' => true,
    'customized' => true,
    'cpage' => true,
    'day' => true,
    'debug' => true,
    'error' => true,
    'exact' => true,
    'feed' => true,
    'hour' => true,
    'link_category' => true,
    'm' => true,
    'minute' => true,
    'monthnum' => true,
    'more' => true,
    'name' => true,
    'nav_menu' => true,
    'nonce' => true,
    'nopaging' => true,
    'offset' => true,
    'order' => true,
    'orderby' => true,
    'p' => true,
    'page' => true,
    'page_id' => true,
    'paged' => true,
    'pagename' => true,
    'pb' => true,
    'perm' => true,
    'post' => true,
    'post__in' => true,
    'post__not_in' => true,
    'post_format' => true,
    'post_mime_type' => true,
    'post_status' => true,
    'post_tag' => true,
    'post_type' => true,
    'posts' => true,
    'posts_per_archive_page' => true,
    'posts_per_page' => true,
    'preview' => true,
    'robots' => true,
    's' => true,
    'search' => true,
    'second' => true,
    'sentence' => true,
    'showposts' => true,
    'static' => true,
    'subpost' => true,
    'subpost_id' => true,
    'tag' => true,
    'tag__and' => true,
    'tag__in' => true,
    'tag__not_in' => true,
    'tag_id' => true,
    'tag_slug__and' => true,
    'tag_slug__in' => true,
    'taxonomy' => true,
    'tb' => true,
    'term' => true,
    'theme' => true,
    'type' => true,
    'w' => true,
    'withcomments' => true,
    'withoutcomments' => true,
    'year' => true );

  function __construct() {
    $this->init();
  }

  public function __get($property) {
    if (property_exists($this, $property)) {
      return $this->$property;
    } else {
      return null;
    }
  }

  public function __set($property, $value) {
    if (property_exists($this, $property)) {
      $this->$property = $value;
    }

    return $this;
  }

  function reg() {
    tr_registry::add($this);

    return $this;
  }

  public function bake() {
    return $this;
  }

  private function init() {
  }

  protected function uses($use) {

    if(isset($use) && !is_array($use)) {
      $use = array($use);
    }

    $current_class = get_class($this);
    foreach($use as $v) :
      if(is_object($v)) {
        $class = get_class($v);
        if( method_exists($this, $class) ) {
          $this->$class($v);
        }
        else {
          die('TypeRocket: You are passing the unsupported object '.$class.' into '. $current_class . '.');
        }
      } else {
        if( method_exists($this, 'tr_uses') ) {
          $this->tr_uses($v);
        }
      }
    endforeach;
  }

  /**
   * Remove slashes from a string
   *
   * @param $v
   */
  protected function unslash(&$v) {
    if(is_string($v)) {
      $v = wp_unslash($v);
    } elseif(is_array($v)) {
      $v = stripslashes_deep($v);
    }
  }

  /**
   * Merge array
   *
   *  Set first value to the new value
   */
  protected function merge(&$arg1, $arg2) {
    if(is_array($arg1) && is_array($arg2)) {
      $arg1 = array_merge($arg1, $arg2);
    }
  }

  /**
   * Replace white space with underscore and make all text lowercase
   *
   * @param $name
   * @param $spacer
   *
   * @return mixed
   */
  protected function sanitize_string(&$name, $spacer = '_') {
    if(is_string($name)) {
      $name = strtolower(trim(sanitize_title($name, '')));
      $pattern = '/(\-+)/';
      $replacement = $spacer;
      $name = preg_replace($pattern,$replacement,$name);
    }
  }

  /**
   * Test for value if there is none die.
   *
   * @param $data
   * @param $error
   * @param string $type
   */
  protected function check($data, $error, $type = 'string') {

    // TODO: look into using WP_Error https://codex.wordpress.org/Class_Reference/WP_Error
    if(!isset($data)) {
      die('TypeRocket Error: '. $error);
    }

    switch($type) {
      case 'array' :
        if(isset($data) && !is_array($data) ) die('TypeRocket Error: '. $error);
        break;
      case 'bool' :
        if(isset($data) && !is_bool($data) ) die('TypeRocket Error: '. $error);
        break;
      default:
        if(isset($data) && !is_string($data)) die('TypeRocket Error: '. $error);
        break;
    }
  }

}