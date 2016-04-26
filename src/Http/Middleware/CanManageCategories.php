<?php
namespace TypeRocket\Http\Middleware;

/**
 * Class OwnsPostOrCanEditPosts
 *
 * Validate that user can owns post or can edit posts and
 * if the user is not invalidate the response.
 *
 * @package TypeRocket\Http\Middleware
 */
class CanManageCategories extends Middleware
{

    public function handle() {

        if ( ! current_user_can( 'manage_categories' ) ) {
            $this->response->setInvalid();
            $this->response->setError( 'auth', false );
            $this->response->setStatus(401);
            $this->response->setMessage( "Sorry, you don't have enough rights." );
        }

        $this->next->handle();
    }

}