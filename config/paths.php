<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Assets URL
    |--------------------------------------------------------------------------
    |
    | The URL where TypeRocket assets can be found.
    |
    */
    'urls' => [
        'assets' => get_theme_file_uri( '/typerocket/wordpress/assets' ),
        'components' => get_theme_file_uri( '/typerocket/wordpress/assets/components' ),
    ],

    /*
    |--------------------------------------------------------------------------
    | TypeRocket Root
    |--------------------------------------------------------------------------
    |
    | The URL where TypeRocket assets can be found.
    |
    */
    'base'  => TR_PATH,

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | The PATH where files are to be cached.
    |
    */
    'cache'  => TR_PATH . '/storage/cache',

    /*
    |--------------------------------------------------------------------------
    | Resources
    |--------------------------------------------------------------------------
    |
    | The PATH were resources can be found.
    |
    */
    'resources'  => TR_PATH . '/resources',

    /*
    |--------------------------------------------------------------------------
    | Views
    |--------------------------------------------------------------------------
    |
    | The PATH were front-end views can be found.
    |
    */
    'views'  => TR_PATH . '/resources/views',

    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    |
    | The PATH were admin pages can be found.
    |
    */
    'pages'  => TR_PATH . '/resources/pages',

    /*
    |--------------------------------------------------------------------------
    | Visuals
    |--------------------------------------------------------------------------
    |
    | The PATH were component visuals can be found.
    |
    */
    'visuals'  => TR_PATH . '/resources/visuals',

    /*
    |--------------------------------------------------------------------------
    | Components
    |--------------------------------------------------------------------------
    |
    | The PATH were components can be found.
    |
    */
    'components'  => TR_PATH . '/resources/components',

    /*
    |--------------------------------------------------------------------------
    | Plugins
    |--------------------------------------------------------------------------
    |
    | The PATH were plugins can be found.
    |
    */
    'plugins' => TR_PATH . '/plugins',

    /*
    |--------------------------------------------------------------------------
    | App
    |--------------------------------------------------------------------------
    |
    | The PATH were the main app can be found.
    |
    */
    'app'  => TR_PATH . '/app',

    /*
    |--------------------------------------------------------------------------
    | Themes
    |--------------------------------------------------------------------------
    |
    | The PATH were theme templates can be found. Used if you install
    | TypeRocket as root.
    |
    */
    'themes'  => TR_PATH . '/resources/themes',

    /*
    |--------------------------------------------------------------------------
    | Migrations
    |--------------------------------------------------------------------------
    |
    | The PATHs for migrations and run migrations. Drivers include: file
    |
    */
    'migrate'  => [
        'driver' => 'file',
        'migrations' => [
            TR_PATH . '/sql/migrations',
        ],
        'run' => TR_PATH . '/sql/run',
    ]

];
