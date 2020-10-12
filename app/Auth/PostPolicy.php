<?php


namespace App\Auth;


use TypeRocket\Auth\Policy;
use TypeRocket\Models\AuthUser;
use TypeRocket\Models\WPPost;

class PostPolicy extends Policy
{

    public function update(AuthUser $auth, WPPost $post)
    {
        if( $auth->isCapable('edit_posts') || $auth->getID() == $post->getUserID()) {
            return true;
        }

        return false;
    }

    public function create(AuthUser $auth )
    {
        if( $auth->isCapable('edit_posts') ) {
            return true;
        }

        return false;
    }

    public function destroy(AuthUser $auth, WPPost $post)
    {
        if( $auth->isCapable('edit_posts') || $post->getUserID() == $auth->getID()) {
            return true;
        }

        return false;
    }

}