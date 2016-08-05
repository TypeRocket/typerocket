<?php
namespace App\Controllers;

use App\Models\Pages;
use TypeRocket\Controllers\PostTypesBaseController;

class PagesController extends PostTypesBaseController
{
    protected $modelClass = Pages::class;
}
