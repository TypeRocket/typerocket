<?php
return [
    /*
    |--------------------------------------------------------------------------
    | App
    |--------------------------------------------------------------------------
    |
    | The PATH were the main app can be found.
    |
    */
    'app' => TYPEROCKET_APP_ROOT_PATH . '/app',

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    |
    | The PATH where files are to be stored.
    |
    */
    'storage' => TYPEROCKET_ALT_PATH . '/storage',

    /*
    |--------------------------------------------------------------------------
    | Logs
    |--------------------------------------------------------------------------
    |
    | The PATH where log files are to be stored.
    |
    */
    'logs' => typerocket_env('TYPEROCKET_LOG_FILE_FOLDER', TYPEROCKET_ALT_PATH . '/storage/logs'),

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | The PATH where files are to be cached.
    |
    */
    'cache' => typerocket_env('TYPEROCKET_CACHE_FILE_FOLDER', TYPEROCKET_ALT_PATH . '/storage/cache'),

    /*
    |--------------------------------------------------------------------------
    | Resources
    |--------------------------------------------------------------------------
    |
    | The PATH were resources can be found.
    |
    */
    'resources' => TYPEROCKET_ALT_PATH . '/resources',

    /*
    |--------------------------------------------------------------------------
    | Views
    |--------------------------------------------------------------------------
    |
    | The PATH were front-end views can be found.
    |
    */
    'views' => TYPEROCKET_ALT_PATH . '/resources/views',

    /*
    |--------------------------------------------------------------------------
    | Themes
    |--------------------------------------------------------------------------
    |
    | The PATH were theme templates can be found. Used if you install
    | TypeRocket as root.
    |
    */
    'themes' => TYPEROCKET_ALT_PATH . '/resources/themes',

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | The PATH were theme templates can be found. Used if you install
    | TypeRocket as root.
    |
    */
    'routes' => TYPEROCKET_ALT_PATH . '/routes',

    /*
    |--------------------------------------------------------------------------
    | Migrations
    |--------------------------------------------------------------------------
    |
    | The PATHs for migrations and run migrations.
    |
    */
    'migrations' => TYPEROCKET_ALT_PATH . '/database/migrations',

    /*
    |--------------------------------------------------------------------------
    | Composer Vendor
    |--------------------------------------------------------------------------
    |
    | The PATH were composer vendor files are located.
    |
    */
    'vendor' => TYPEROCKET_PATH . '/vendor',

    /*
    |--------------------------------------------------------------------------
    | Core
    |--------------------------------------------------------------------------
    |
    | The PATH were composer vendor files are located.
    |
    */
    'core' => TYPEROCKET_PATH . '/vendor/typerocket/core',

    /*
    |--------------------------------------------------------------------------
    | Pro Core
    |--------------------------------------------------------------------------
    |
    | The PATH were pro composer vendor files are located.
    |
    */
    'pro' => TYPEROCKET_PATH . '/vendor/typerocket/professional',

    /*
    |--------------------------------------------------------------------------
    | Assets
    |--------------------------------------------------------------------------
    |
    | The PATH where TypeRocket theme and build assets are located.
    |
    */
    'assets' => TYPEROCKET_PATH . '/wordpress/assets',
];
