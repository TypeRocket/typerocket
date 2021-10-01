<?php
namespace App\Models;

use TypeRocket\Models\WPTerm;

class Tag extends WPTerm
{
    public const TAXONOMY = 'post_tag';

    public function posts()
    {
        return $this->belongsToPost(Post::class);
    }
}