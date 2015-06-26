<?php
namespace TypeRocket\Plugin;

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
class Loader
{
    public $plugins = null;

    function __construct(Collection $plugins)
    {
        $this->setCollection($plugins);
    }

    function setCollection(Collection $collection) {
        $this->plugins = apply_filters('tr_plugins_collection', $collection->plugins);
    }

    function load()
    {
        $plugins_list = $this->plugins;
	    $paths = \TypeRocket\Config::getPaths();

        foreach ($plugins_list as $plugin) {
            $folder = $paths['plugins'] . '/' . $plugin . '/';

            if (file_exists($folder . 'init.php')) {
                include $folder . 'init.php';
            }
        }
    }

}