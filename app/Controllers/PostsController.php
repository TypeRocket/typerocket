<?php
namespace App\Controllers;

use App\Models\Posts;
use TypeRocket\Controllers\PostTypesBaseController;

class PostsController extends PostTypesBaseController
{
    protected $modelClass = Posts::class;
}
