<?php
namespace App\Controllers;

use \TypeRocket\Controllers\TaxonomiesBaseController,
    \App\Models\Categories;

class CategoriesController extends TaxonomiesBaseController
{
    protected $modelClass = Categories::class;
}