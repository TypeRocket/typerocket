<?php

namespace App\Controllers;

use App\Models\Comment;
use TypeRocket\Controllers\WPCommentController;

class CommentController extends WPCommentController
{
    protected $modelClass = Comment::class;
}