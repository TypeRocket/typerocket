<?php
namespace App\Models;

use TypeRocket\Models\WPPost;

class Post extends WPPost
{
    public const POST_TYPE = 'post';

    public function categories()
    {
        return $this->belongsToTaxonomy(Category::class, Category::TAXONOMY);
    }

    public function tags()
    {
        return $this->belongsToTaxonomy(Tag::class, Tag::TAXONOMY);
    }
}
