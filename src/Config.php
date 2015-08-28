<?php
namespace TypeRocket;

class Config
{

    static private $paths = null;
    static private $debug = false;
    static private $folder = null;
    static private $seed = null;
    static private $plugins = null;

    /**
     * Set initial values
     */
    public function __construct()
    {
        if (self::$paths === null) {
            self::$debug   = defined( 'TR_DEBUG' ) ? TR_DEBUG : false;
            self::$folder  = defined( 'TR_FOLDER' ) ? TR_FOLDER : 'typerocket';
            self::$seed    = defined( 'TR_SEED' ) ? TR_SEED : 'replaceThis';
            self::$plugins = defined( 'TR_PLUGINS' ) ? TR_PLUGINS : '';
            self::$paths   = $this->defaultPaths();
        }
    }

    /**
     * Get paths array
     *
     * @return mixed|null|void
     */
    static public function getPaths()
    {
        return self::$paths;
    }

    /**
     * Get debug status
     *
     * @return bool
     */
    static public function getDebugStatus()
    {
        return self::$debug;
    }

    /**
     * Get Seed
     *
     * @return null|string
     */
    static public function getSeed()
    {
        return self::$seed;
    }

    /**
     * Get the TypeRocket folder name
     *
     * @return null|string
     */
    static public function getFolder()
    {
        return self::$folder;
    }

    /**
     * Get array of plugins
     *
     * @return array
     */
    static public function getPlugins()
    {
        return explode( '|', self::$plugins );
    }

    /**
     * Set default paths
     *
     * @return array
     */
    private function defaultPaths()
    {
        return array(
            'assets'  => __DIR__ . '/../assets',
            'plugins' => defined( 'TR_PLUGINS_FOLDER_PATH' ) ? TR_PLUGINS_FOLDER_PATH : __DIR__ . '/../plugins',
            'matrix'  => defined( 'TR_MATRIX_FOLDER_PATH' ) ? TR_MATRIX_FOLDER_PATH : __DIR__ . '/../matrix',
            'extend'  => defined( 'TR_APP_FOLDER_PATH' ) ? TR_APP_FOLDER_PATH : __DIR__ . '/../app',
            'urls'    => array(
                'theme'   => get_stylesheet_directory_uri(),
                'assets'  => defined( 'TR_ASSETS_URL' ) ? TR_ASSETS_URL : get_stylesheet_directory_uri() . '/' . self::$folder . '/assets',
                'plugins' => defined( 'TR_PLUGINS_URL' ) ? TR_PLUGINS_URL : get_stylesheet_directory_uri() . '/' . self::$folder . '/plugins'
            )
        );
    }

}
