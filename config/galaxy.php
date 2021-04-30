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
    | Load WordPress
    |--------------------------------------------------------------------------
    |
    | Load WordPress and run commands after_setup_theme if WordPress is found.
    | You can run `TYPEROCKET_GALAXY_LOAD_WP=no php galaxy` to skip loading
    | WordPress for the current running command.
    |
    | Options include: yes, no
    |
    */
    'wordpress_load' => typerocket_env('TYPEROCKET_GALAXY_LOAD_WP', 'yes', true),

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
