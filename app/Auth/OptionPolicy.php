<?php
namespace App\Auth;

use \TypeRocket\Auth\Policy;
use TypeRocket\Models\AuthUser;

class OptionPolicy extends Policy
{

    public function update(AuthUser $auth)
    {
        if( $auth->isCapable('manage_options') ) {
            return true;
        }

        return false;
    }

    public function create(AuthUser $auth)
    {
        if( $auth->isCapable('manage_options') ) {
            return true;
        }

        return false;
    }

    public function destroy(AuthUser $auth)
    {
        if( $auth->isCapable('manage_options') ) {
            return true;
        }

        return false;
    }

    
}