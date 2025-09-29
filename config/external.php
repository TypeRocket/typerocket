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
        'geo_api_endpoint' => typerocket_env('TYPEROCKET_GOOGLE_MAPS_API_KEY', 'https://maps.googleapis.com/maps/api/geocode/json'),
        'api_key' => typerocket_env('TYPEROCKET_GOOGLE_MAPS_API_KEY'),
        'map_zoom' => 15,
        'ui' => false
    ]
];