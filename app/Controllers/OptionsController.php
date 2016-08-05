<?php

namespace App\Controllers;

use App\Models\Options;
use TypeRocket\Controllers\OptionsBaseController;

class OptionsController extends OptionsBaseController
{
    protected $modelClass = Options::class;
}