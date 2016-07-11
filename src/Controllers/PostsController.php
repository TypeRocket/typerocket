<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\PostsModel;

class PostsController extends PostTypesController
{
    protected $modelClass = PostsModel::class;
}
