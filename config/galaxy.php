<?php
return [
    /*
    |--------------------------------------------------------------------------
    | WordPress
    |--------------------------------------------------------------------------
    |
    | Set to the WordPress root directory. This will enable new WP specific
    | Galaxy commands like: SQL, Migrations, and Flushing Permalinks
    |
    | Example of root installation: TYPEROCKET_PATH . '/wordpress'
    |
    */
    'wordpress' => \TypeRocket\Utility\Helper::wordPressRootPath(),

    /*
    |--------------------------------------------------------------------------
    | Commands
    |--------------------------------------------------------------------------
    |
    | Add your custom commands for Galaxy. TypeRocket commands use Symfony
    | framework see http://symfony.com/doc/current/console.html
    |
    */
    'commands' => [
    ]
];
