<?php

namespace App\Controllers;

use App\Models\User;
use TypeRocket\Controllers\WPUserController;

class UserController extends WPUserController
{
    protected $modelClass = User::class;
}