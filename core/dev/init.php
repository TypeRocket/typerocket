<?php
class tr_dev extends tr_base {

  function make() {
    add_filter('admin_footer_text', array($this,'tr_remove_footer_admin'));
    add_action('admin_menu', array($this, 'menu'));
    add_action('admin_init', array($this, 'add_css'));
  }

  function tr_remove_footer_admin () {
    echo 'TypeRocket developer mode is on!';
  }

  public function add_css() {
    wp_enqueue_style( 'typerocket-dev', tr::$paths['urls']['core'] . '/dev/typerocket-dev.css' );
  }

  public function menu() {
    add_menu_page( 'Dev', 'Dev', 'manage_options', 'tr_dev', array($this, 'page'));
  }

  function page() {
      include(__DIR__ . '/page.php');
  }

}

$tr_dev_plugin = new tr_dev();
$tr_dev_plugin->make();
unset($tr_dev_plugin);