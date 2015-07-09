<?php
namespace TypeRocket\Plugin;

/*
|--------------------------------------------------------------------------
| Plugin Collection
|--------------------------------------------------------------------------
|
| Load plugins. All plugins should live in a dir and must include an
| init.php file to get loaded. There are no file only plugins folders
| must be used.
|
*/
class Collection
{
    public $plugins = array();

    function __construct(array $plugins)
    {
	    foreach($plugins as $plugin) {
		    $this->addPlugin($plugin);
	    }
    }

    function addPlugin($string) {
        if(is_string($string)) {
            array_push($this->plugins, $string);
        }
    }

}