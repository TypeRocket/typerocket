<?php
namespace App\Models;

use TypeRocket\Models\WPPost;

class Post extends WPPost
{
    protected $postType = 'post';

    public function categories()
    {
        return $this->belongsToTaxonomy(Category::class, 'category');
    }

    public function tags()
    {
        return $this->belongsToTaxonomy(Tag::class, 'post_tag');
    }
}
