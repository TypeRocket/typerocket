<?php
namespace TypeRocket;

class Config {
    static private $paths = null;
    static private $debug = false;
    static private $folder = null;
    static private $seed = null;
    static private $plugins = null;

    public function __construct() {
	    if(self::$paths === null) {
		    self::$debug = defined('TR_DEBUG') ? TR_DEBUG : false;
		    self::$folder = defined('TR_FOLDER') ? TR_FOLDER : 'typerocket';
		    self::$seed = defined('TR_SEED') ? TR_SEED : 'replaceThis';
		    self::$plugins = defined('TR_PLUGINS') ? TR_PLUGINS : '';
		    self::$paths = apply_filters('tr_paths', $this->defaultPaths() );
	    }
    }

    static public function getPaths() {
        return self::$paths;
    }

    static public function getDebugStatus() {
        return self::$debug;
    }

    static public function getSeed() {
        return self::$seed;
    }

    static public function getFolder() {
        return self::$folder;
    }

	static public function getPlugins() {
		return explode('|', self::$plugins);
	}

    private function defaultPaths() {
        return array(
            'assets'  => __DIR__ . '/../assets',
            'plugins' => __DIR__ . '/../plugins',
            'urls'    => array(
                'theme'   => get_stylesheet_directory_uri(),
                'assets'  => get_stylesheet_directory_uri() . '/' . self::$folder . '/assets',
                'plugins' => get_stylesheet_directory_uri() . '/' . self::$folder . '/plugins'
            )
        );
    }

}
