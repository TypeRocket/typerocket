<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Frontend
    |--------------------------------------------------------------------------
    |
    | Determine frontend settings
    |
    */
    'frontend' => [
        'assets' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | System Tooling
    |--------------------------------------------------------------------------
    |
    | Enable system tools.
    |
    */
    'system' => [
        'ssl' => false,
        'on_demand_image_sizing' => false
    ],

    /*
    |--------------------------------------------------------------------------
    | Post Messages
    |--------------------------------------------------------------------------
    |
    | Determine admin settings
    |
    */
    'admin' => [
        'post_messages' => true,
    ],
    

    'routes' => [
        /*
        |--------------------------------------------------------------------------
        | Routing Hook
        |--------------------------------------------------------------------------
        |
        | Determine how routes are loaded and used. If you want routes
        | loaded instantly set hook to _instant_. Other hook options
        | include: muplugins_loaded, plugins_loaded, or setup_theme
        |
        | Default option: typerocket_loaded
        |
        */
        'hook' => 'typerocket_loaded',

        /*
        |--------------------------------------------------------------------------
        | Match Routes
        |--------------------------------------------------------------------------
        |
        | Routing rules and configurations. Updating these settings can effect
        | third-party and official plugins or extensions. Only update these
        | settings if you are sure it will not break your site.
        |
        | Match options: null or 'site_url'
        |
        */
        'match' => 'site_url',
    ],
];
