<?php
namespace App\Services;

use TypeRocket\Services\AuthorizerService;

class AuthService extends AuthorizerService
{
    protected $policies = [
        // Models
        '\App\Models\Post' => '\App\Auth\PostPolicy',
        '\App\Models\Page' => '\App\Auth\PagePolicy',
        '\App\Models\Attachment' => '\App\Auth\PostPolicy',
        '\App\Models\Tag' => '\App\Auth\TermPolicy',
        '\App\Models\Category' => '\App\Auth\TermPolicy',
        '\App\Models\Option' => '\App\Auth\OptionPolicy',
        '\App\Models\User' => '\App\Auth\UserPolicy',
        '\App\Models\Comment' => '\App\Auth\CommentPolicy',

        // TypeRocket
        '\TypeRocket\Models\WPPost' => '\App\Auth\PostPolicy',
        '\TypeRocket\Models\WPTerm' => '\App\Auth\TermPolicy',
        '\TypeRocket\Models\WPUser' => '\App\Auth\UserPolicy',
        '\TypeRocket\Models\WPComment' => '\App\Auth\CommentPolicy',
        '\TypeRocket\Models\WPOption' => '\App\Auth\OptionPolicy',
    ];
}