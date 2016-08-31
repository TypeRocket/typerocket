<?php
namespace App\Models;

use TypeRocket\Models\WPTerm;

class Category extends WPTerm
{
    protected $taxonomy = 'category';
}
