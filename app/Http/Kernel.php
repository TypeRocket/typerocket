<?php
namespace App\Http;

use App\Http\Middleware\VerifyNonce;
use TypeRocket\Http\Middleware\AuthAdmin;
use TypeRocket\Http\Middleware\AuthRead;
use TypeRocket\Http\Middleware\CanManageCategories;
use TypeRocket\Http\Middleware\CanManageOptions;
use TypeRocket\Http\Middleware\IsUserOrCanEditUsers;
use TypeRocket\Http\Middleware\OwnsCommentOrCanEditComments;
use TypeRocket\Http\Middleware\OwnsPostOrCanEditPosts;

class Kernel extends \TypeRocket\Http\Kernel
{
    public $middleware = [
        'hookGlobal' => [],
        'restApiFallback' =>
            [ AuthAdmin::class ],
        'resourceGlobal' =>
            [  VerifyNonce::class ],
        'user' =>
            [ IsUserOrCanEditUsers::class ],
        'post' =>
            [ OwnsPostOrCanEditPosts::class ],
        'comment' =>
            [ OwnsCommentOrCanEditComments::class ],
        'option' =>
            [ CanManageOptions::class ],
        'term' =>
            [ CanManageCategories::class ],
    ];
}
