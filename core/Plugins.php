<?php
namespace TypeRocket;

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
class Plugins
{

    public function run(array $array)
    {
        $this->loader($array);
    }

    private function loader(array $plugins_list)
    {

        $plugins_list = apply_filters('tr_plugins_array', $plugins_list);

        foreach ($plugins_list as $plugin) {
            $folder = \tr::$paths['plugins'] . '/' . $plugin . '/';

            if (file_exists($folder . 'init.php')) {
                include $folder . 'init.php';
            }
        }
    }

}