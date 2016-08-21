<?php
namespace App\Controllers;

use \App\Models\Category;
use \TypeRocket\Controllers\WPTermController;

class CategoryController extends WPTermController
{
    protected $modelClass = Category::class;
}