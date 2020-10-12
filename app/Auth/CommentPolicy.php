<?php
namespace App\Auth;

use \TypeRocket\Auth\Policy;
use TypeRocket\Models\AuthUser;
use TypeRocket\Models\WPComment;

class CommentPolicy extends Policy
{
    public function update(AuthUser $auth, WPComment $comment)
    {
        if( $auth->isCapable('edit_posts') || $comment->getUserID() == $auth->getID()) {
            return true;
        }

        return false;
    }

    public function create(AuthUser $auth)
    {
        if( $auth->isCapable('edit_posts') ) {
            return true;
        }

        return false;
    }

    public function destroy(AuthUser $auth, WPComment $comment)
    {
        if( $auth->isCapable('edit_posts') || $comment->getUserID() == $auth->getID()) {
            return true;
        }

        return false;
    }
}