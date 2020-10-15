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
    'assets' => \TypeRocket\Utility\Helper::assetsUrlBuild(),

    /*
    |--------------------------------------------------------------------------
    | Components
    |--------------------------------------------------------------------------
    |
    | The URL where TypeRocket component assets are found.
    |
    */
    'components' => \TypeRocket\Utility\Helper::assetsUrlBuild( '/components' ),

    /*
    |--------------------------------------------------------------------------
    | Typerocket Assets
    |--------------------------------------------------------------------------
    |
    | The URL where TypeRocket Core assets are found.
    |
    */
    'typerocket' => \TypeRocket\Utility\Helper::assetsUrlBuild( '/typerocket' ),
];
