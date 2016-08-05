<?php

namespace App\Controllers;

use App\Models\Users;
use TypeRocket\Controllers\UsersBaseController;

class UsersController extends UsersBaseController
{
    protected $modelClass = Users::class;
}