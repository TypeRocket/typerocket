<?php

namespace App\Models;

use TypeRocket\Models\TaxonomiesModel;

class Tags extends TaxonomiesModel
{
    protected $taxonomy = 'post_tag';
}