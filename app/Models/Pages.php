<?php
namespace App\Models;

use TypeRocket\Models\PostTypesModel;

class Pages extends PostTypesModel
{
    /** @var string $postType set as correct post type for model */
    protected $postType = 'page';
}
