<?php
namespace App\Controllers;

use \TypeRocket\Controllers\TaxonomiesBaseController;
use \App\Models\Categories;

class CategoriesController extends TaxonomiesBaseController
{
    protected $modelClass = Categories::class;
}