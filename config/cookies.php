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
    ]
];