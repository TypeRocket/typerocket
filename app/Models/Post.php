<?php
namespace App\Models;

use TypeRocket\Models\WPPost;

class Post extends WPPost
{
    protected $postType = 'post';
}
