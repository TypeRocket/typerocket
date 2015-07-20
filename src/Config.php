<?php
namespace TypeRocket;

class Config
{

    static private $paths = null;
    static private $debug = false;
    static private $folder = null;
    static private $seed = null;
    static private $plugins = null;

    public function __construct()
    {
        if (self::$paths === null) {
            self::$debug   = defined( 'TR_DEBUG' ) ? TR_DEBUG : false;
            self::$folder  = defined( 'TR_FOLDER' ) ? TR_FOLDER : 'typerocket';
            self::$seed    = defined( 'TR_SEED' ) ? TR_SEED : 'replaceThis';
            self::$plugins = defined( 'TR_PLUGINS' ) ? TR_PLUGINS : '';
            self::$paths   = apply_filters( 'tr_paths', $this->defaultPaths() );
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
            'plugins' => defined( 'TR_PLUGINS_FOLDER' ) ? TR_PLUGINS_FOLDER : __DIR__ . '/../plugins',
            'matrix'  => defined( 'TR_MATRIX_FOLDER' ) ? TR_MATRIX_FOLDER : __DIR__ . '/../matrix',
            'urls'    => array(
                'theme'   => get_stylesheet_directory_uri(),
                'assets'  => get_stylesheet_directory_uri() . '/' . self::$folder . '/assets',
                'plugins' => get_stylesheet_directory_uri() . '/' . self::$folder . '/plugins'
            )
        );
    }

}
