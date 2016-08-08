<?php
namespace App\Controllers;

use \App\Models\Categories;
use \TypeRocket\Controllers\TaxonomiesBaseController;

class CategoriesController extends TaxonomiesBaseController
{
    protected $modelClass = Categories::class;
}