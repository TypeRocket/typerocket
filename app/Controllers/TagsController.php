<?php
namespace App\Controllers;

use App\Models\Tags;
use TypeRocket\Controllers\TaxonomiesBaseController;

class TagsController extends TaxonomiesBaseController
{
    protected $modelClass = Tags::class;
}