<?php

namespace App\Controllers;

use App\Models\Option;
use TypeRocket\Controllers\WPOptionController;

class OptionController extends WPOptionController
{
    protected $modelClass = Option::class;
}