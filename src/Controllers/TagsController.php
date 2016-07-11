<?php
namespace TypeRocket\Controllers;

use TypeRocket\Models\TagsModel;

class TagsController extends TaxonomiesController
{
    protected $modelClass = TagsModel::class;
}