<?php
namespace App\Models;

use TypeRocket\Models\WPPost;

class Page extends WPPost
{
    public $postType = 'page';
}
