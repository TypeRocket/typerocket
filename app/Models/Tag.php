<?php

namespace App\Models;

use TypeRocket\Models\WPTerm;

class Tag extends WPTerm
{
    public $taxonomy = 'post_tag';
}