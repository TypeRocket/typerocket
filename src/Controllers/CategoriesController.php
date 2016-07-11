<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\CategoriesModel;

class CategoriesController extends TaxonomiesController
{
    protected $modelClass = CategoriesModel::class;
}