<?php
namespace TypeRocket\Plugin;

use \TypeRocket\Config;

/**
 * Plugin Loader
 *
 * Load plugins. All plugins should live in a folder and must include an
 * init.php file to be loaded. There are no file-only-plugins, folders
 * must be used.
 */
class Loader
{
    public $plugins = null;

    /**
     * Set Plugins on construction
     *
     * @param PluginCollection $plugins
     */
    public function __construct(PluginCollection $plugins)
    {
        $this->setCollection($plugins);
    }

    /**
     * Set the collection of plugins
     *
     * @param PluginCollection $collection
     */
    private function setCollection(PluginCollection $collection) {
        $this->plugins = apply_filters('tr_plugins_collection', $collection);
    }

    /**
     * Load Plugins
     */
    public function load()
    {
        $plugins_list = $this->plugins;
	    $paths = Config::getPaths();

        foreach ($plugins_list as $plugin) {
            $folder = $paths['plugins'] . '/' . $plugin . '/';

            if (file_exists($folder . 'init.php')) {
                /** @noinspection PhpIncludeInspection */
                include $folder . 'init.php';
            }
        }
    }

}
