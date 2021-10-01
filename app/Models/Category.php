<?php
namespace App\Models;

use TypeRocket\Models\WPTerm;

class Category extends WPTerm
{
    public const TAXONOMY = 'category';

    public function posts()
    {
        return $this->belongsToPost(Post::class);
    }
}
