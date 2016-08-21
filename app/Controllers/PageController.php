<?php
namespace App\Controllers;

use App\Models\Page;
use TypeRocket\Controllers\WPPostController;

class PageController extends WPPostController
{
    protected $modelClass = Page::class;
}
