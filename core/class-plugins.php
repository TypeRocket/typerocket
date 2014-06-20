<?php
/*
|--------------------------------------------------------------------------
| Plugin Loader
|--------------------------------------------------------------------------
|
| Load plugins. All plugins should live in a dir and must include an
| init.php file to get loaded. There are no file only plugins folders
| must be used.
|
*/
class tr_plugin extends tr_base {

  public function run($array) {
      $this->loader($array);
  }

  private function loader($array) {
    $this->check($array, 'Plugins config var $tr_plugins must be an array. An empty array will work.', 'array');

    $array = apply_filters('tr_plugins_array', $array);

    foreach($array as $plugin) :
      $folder = tr::$paths['plugins'] . '/' . $plugin . '/';

      if (file_exists($folder . 'init.php')) {
        include $folder . 'init.php';
      }
    endforeach;
  }

}