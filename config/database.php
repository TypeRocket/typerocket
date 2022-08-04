<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Default Database Connection
    |--------------------------------------------------------------------------
    |
    | This option defines the default database driver that is used when a query
    | or model is instantiated. See the list of connections below for more
    | details or add your own to the list. There is one by default.
    |
    */
    'default' => typerocket_env('TYPEROCKET_DATABASE_DEFAULT', 'wp'),

    /*
    |--------------------------------------------------------------------------
    | Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the database drivers for your application.
    |
    | Available Drivers: "wp"
    */
    'drivers' => [
        'wp' => [
            'driver' => '\TypeRocket\Database\Connectors\WordPressCoreDatabaseConnector',
        ],

        'alt' => [
            'driver' => '\TypeRocket\Database\Connectors\CoreDatabaseConnector',
            'username' => '',
            'password' => '',
            'database' => '',
            'host' => '',
        ],
    ]
];