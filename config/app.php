<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Enabled Plugins
    |--------------------------------------------------------------------------
    |
    | The folder names of the TypeRocket plugins you wish to enable.
    |
    */
    'plugins' => [
        'seo',
        'dev',
        'theme-options',
        'builder',
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug
    |--------------------------------------------------------------------------
    |
    | Turn on Debugging for TypeRocket. Set to false to disable.
    |
    */
    'debug' => true,

    /*
    |--------------------------------------------------------------------------
    | Seed
    |--------------------------------------------------------------------------
    |
    | A 'random' string of text to help with security from time to time.
    |
    */
    'seed' => 'PUT_TYPEROCKET_SEED_HERE',

    /*
    |--------------------------------------------------------------------------
    | Icons
    |--------------------------------------------------------------------------
    |
    | The icon class to use for the admin.
    |
    */
    'icons' => \TypeRocket\Elements\Icons::class
];