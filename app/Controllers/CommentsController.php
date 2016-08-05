<?php

namespace App\Controllers;

use App\Models\Comments;
use TypeRocket\Controllers\CommentsBaseController;

class CommentsController extends CommentsBaseController
{
    protected $modelClass = Comments::class;
}