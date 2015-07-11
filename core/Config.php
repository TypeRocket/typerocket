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
		    self::$paths = apply_filters('tr_paths', require __DIR__ . '/../app/paths.php' );
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

}
