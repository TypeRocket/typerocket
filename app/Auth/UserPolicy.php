<?php
namespace App\Auth;

use \TypeRocket\Auth\Policy;
use TypeRocket\Models\AuthUser;
use TypeRocket\Models\WPUser;

class UserPolicy extends Policy
{
    public function update(AuthUser $auth, WPUser $user)
    {
        if( $auth->isCapable('edit_users') || $user->getID() != $auth->getID()) {
            return true;
        }

        return false;
    }

    public function create(AuthUser $auth)
    {
        if( $auth->isCapable('create_users') ) {
            return true;
        }

        return false;
    }

    public function destroy(AuthUser $auth, WPUser $user)
    {
        if( $auth->isCapable('delete_users') || $user->getID() != $auth->getID()) {
            return true;
        }

        return false;
    }
}