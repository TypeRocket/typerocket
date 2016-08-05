<?php

namespace App\Http;

use TypeRocket\Http\Middleware\AuthAdmin;
use TypeRocket\Http\Middleware\AuthRead;
use TypeRocket\Http\Middleware\CanManageCategories;
use TypeRocket\Http\Middleware\CanManageOptions;
use TypeRocket\Http\Middleware\IsUserOrCanEditUsers;
use TypeRocket\Http\Middleware\OwnsCommentOrCanEditComments;
use TypeRocket\Http\Middleware\OwnsPostOrCanEditPosts;

class Kernel extends \TypeRocket\Http\Kernel
{
    protected $middleware = [
        'hookGlobal' =>
            [ AuthRead::class ],
        'resourceGlobal' =>
            [
                AuthRead::class,
                Middleware\VerifyNonce::class
            ],
        'noResource' =>
            [ AuthAdmin::class ],
        'users' =>
            [ IsUserOrCanEditUsers::class ],
        'posts' =>
            [ OwnsPostOrCanEditPosts::class ],
        'pages' =>
            [ OwnsPostOrCanEditPosts::class ],
        'comments' =>
            [ OwnsCommentOrCanEditComments::class ],
        'options' =>
            [ CanManageOptions::class ],
        'categories' =>
            [ CanManageCategories::class ],
        'tags' =>
            [ CanManageCategories::class ]
    ];
}