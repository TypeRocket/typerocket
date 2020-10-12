<?php


namespace App\Auth;


use App\Models\Page;
use TypeRocket\Auth\Policy;
use TypeRocket\Models\AuthUser;

class PagePolicy extends Policy
{

    public function update(AuthUser $auth, Page $page)
    {
        if( $auth->isCapable('edit_pages') || $auth->getID() == $page->getUserID()) {
            return true;
        }

        return false;
    }

    public function create(AuthUser $auth )
    {
        if( $auth->isCapable('edit_pages') ) {
            return true;
        }

        return false;
    }

    public function destroy(AuthUser $auth, Page $page)
    {
        if( $auth->isCapable('delete_pages') || $page->getUserID() == $auth->getID()) {
            return true;
        }

        return false;
    }

}