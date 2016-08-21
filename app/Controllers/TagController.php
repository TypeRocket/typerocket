<?php
namespace App\Controllers;

use App\Models\Tag;
use TypeRocket\Controllers\WPTermController;

class TagController extends WPTermController
{
    protected $modelClass = Tag::class;
}