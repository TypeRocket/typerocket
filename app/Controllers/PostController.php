<?php
namespace App\Controllers;

use App\Models\Post;
use TypeRocket\Controllers\WPPostController;

class PostController extends WPPostController
{
    protected $modelClass = Post::class;
}
