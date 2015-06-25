<?php
namespace TypeRocket;

class Lists {

  static private $states = array(
    'AL'=>"Alabama",
    'AK'=>"Alaska",
    'AZ'=>"Arizona",
    'AR'=>"Arkansas",
    'CA'=>"California",
    'CO'=>"Colorado",
    'CT'=>"Connecticut",
    'DE'=>"Delaware",
    'DC'=>"District Of Columbia",
    'FL'=>"Florida",
    'GA'=>"Georgia",
    'HI'=>"Hawaii",
    'ID'=>"Idaho",
    'IL'=>"Illinois",
    'IN'=>"Indiana",
    'IA'=>"Iowa",
    'KS'=>"Kansas",
    'KY'=>"Kentucky",
    'LA'=>"Louisiana",
    'ME'=>"Maine",
    'MD'=>"Maryland",
    'MA'=>"Massachusetts",
    'MI'=>"Michigan",
    'MN'=>"Minnesota",
    'MS'=>"Mississippi",
    'MO'=>"Missouri",
    'MT'=>"Montana",
    'NE'=>"Nebraska",
    'NV'=>"Nevada",
    'NH'=>"New Hampshire",
    'NJ'=>"New Jersey",
    'NM'=>"New Mexico",
    'NY'=>"New York",
    'NC'=>"North Carolina",
    'ND'=>"North Dakota",
    'OH'=>"Ohio",
    'OK'=>"Oklahoma",
    'OR'=>"Oregon",
    'PA'=>"Pennsylvania",
    'RI'=>"Rhode Island",
    'SC'=>"South Carolina",
    'SD'=>"South Dakota",
    'TN'=>"Tennessee",
    'TX'=>"Texas",
    'UT'=>"Utah",
    'VT'=>"Vermont",
    'VA'=>"Virginia",
    'WA'=>"Washington",
    'WV'=>"West Virginia",
    'WI'=>"Wisconsin",
    'WY'=>"Wyoming");

  static private $timezones = null;
  static private $pages = null;
  static private $categories = null;
  static private $tags = null;
  static private $users = null;
  static private $taxonomies = null;
  static private $terms = null;
  static private $post_types = null;

  static function timezones($flip = true) {
    if(self::$timezones == null) {
      self::$timezones = DateTimeZone::listIdentifiers(DateTimeZone::AMERICA);
    }

    if($flip !== false) {
      self::$timezones = array_flip(self::$timezones);
    }

    return self::$timezones;
  }

  static function states($flip = true) {
    if($flip !== false) {
      self::$states = array_flip(self::$states);
    }
    return self::$states;
  }

  static function pages($args = array(), $v = 'post_title', $k = 'ID') {
    $defaults = array(
      'child_of' => 0, 'sort_order' => 'ASC',
      'sort_column' => 'post_parent,menu_order', 'hierarchical' => 1,
      'exclude' => array(), 'include' => array(),
      'meta_key' => '', 'meta_value' => '',
      'authors' => '', 'parent' => -1, 'exclude_tree' => '',
      'number' => '', 'offset' => 0,
      'post_type' => 'page', 'post_status' => 'publish',
    );
    $args = array_merge($defaults, $args);
    $pages = new WP_Query($args);

    $opt = array();
    foreach ($pages->posts as $page) {
      $opt[$page->$v] = $page->$k;
    }
    self::$pages = $opt;
    return self::$pages;
  }

  static function taxonomies($args = array(), $v = 'label', $k = 'name') {
    $opt = array();
    $defaults = array(
      'public'   => true
    );
    $args = array_merge($defaults, $args);
    $taxonomies = get_taxonomies($args, 'objects');
    foreach ($taxonomies as $taxonomy) {
      $opt[$taxonomy->$v] = $taxonomy->$k;
    }
    self::$taxonomies = $opt;
    return self::$taxonomies;
  }

  static function categories($args = array(), $v = 'cat_name', $k = 'cat_ID') {
    $opt = array();
    $categories = get_categories($args);
    foreach ($categories as $category) {
      $opt[$category->$v] = $category->$k;
    }
    self::$categories = $opt;
    return self::$categories;
  }

  static function tags($args = array(), $v = 'name', $k = 'term_id') {
    $opt = array();
    $tags = get_tags($args);
    foreach ( $tags as $tag ) {
      $opt[$tag->$v] = $tag->$k;
    }
    self::$tags = $opt;
    return self::$tags;
  }

  static function terms($taxonomies = array(), $args = array(), $v = 'name', $k = 'term_id') {
    $opt = array();
    $defaults = array(
      'hide_empty' => 0
    );
    $args = array_merge($defaults, $args);
    $terms = get_terms($taxonomies, $args);
    foreach ($terms as $term) {
      $opt[$term->$v] = $term->$k;
    }
    self::$terms = $opt;
    return self::$terms;
  }

  static function post_types($args = array(), $v = 'label', $k = 'name') {
    $opt = array();
    $defaults = array(
      'public' => true
    );
    $args = array_merge($defaults, $args);
    $post_types = get_post_types($args, 'objects');
    foreach ( $post_types as $type ) {
      $opt[$type->$v] = $type->$k;
    }
    self::$tags = $opt;
    return self::$tags;
  }

  static function users($args = array(), $v = 'user_email', $k = 'ID') {
    $opt = array();
    $users = get_users($args);
    foreach ($users as $user) {
      $opt[$user->$v] = $user->$k;
    }
    self::$users = $opt;
    return self::$users;
  }

}