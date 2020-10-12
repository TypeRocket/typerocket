<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Assets
    |--------------------------------------------------------------------------
    |
    | The URL where TypeRocket assets are found.
    |
    */
    'assets' => tr_assets_url_build(),

    /*
    |--------------------------------------------------------------------------
    | Components
    |--------------------------------------------------------------------------
    |
    | The URL where TypeRocket component assets are found.
    |
    */
    'components' => tr_assets_url_build( '/components' ),

    /*
    |--------------------------------------------------------------------------
    | Typerocket Assets
    |--------------------------------------------------------------------------
    |
    | The URL where TypeRocket Core assets are found.
    |
    */
    'typerocket' => tr_assets_url_build( '/typerocket' ),
];
