<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Auth Cookies
    |--------------------------------------------------------------------------
    |
    | When \TypeRocket\Services\SecureAuthCookiesService is enabled these
    | options will be used. Otherwise, you can ignore these.
    |
    */
    'auth' => [

        /*
        |--------------------------------------------------------------------------
        | SameSite Policy
        |--------------------------------------------------------------------------
        |
        | WordPress uses old PHP settings for its auth cookies. If you are using
        | PHP 7.3 or greater you can set the `SameSite` value for cookies. This
        | option defines the value of `SameSite`.
        |
        | Options: None, Lax or Strict
        |
        */
        'same_site' => 'Lax',

        /*
        |--------------------------------------------------------------------------
        | Timeout Period - Action Scheduler
        |--------------------------------------------------------------------------
        |
        | By default, WordPress adds `X-Frame-Options: SAMEORIGIN`. However, these
        | headers are often set by the web server instead. Set this option as
        | `false` to disable WordPress' x-frame-options.
        |
        */
        'x_frame_options' => true,
    ]
];