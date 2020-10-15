<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Google Maps
    |--------------------------------------------------------------------------
    |
    | API Configurations for Google Maps.
    |
    | @link https://developers.google.com/maps/documentation/javascript/tutorial
    |
    */
    'google_maps' => [
        'api_key' => typerocket_env('TYPEROCKET_GOOGLE_MAPS_API_KEY'),
        'map_zoom' => 15,
        'ui' => false
    ]
];