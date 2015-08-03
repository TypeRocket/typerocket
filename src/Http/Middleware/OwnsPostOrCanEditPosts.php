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
class OwnsPostOrCanEditPosts extends Middleware
{

    public function handle() {

        $post  = get_post( $this->request->getResourceId() );
        $currentUser = wp_get_current_user();

        if ($post->post_author != $currentUser->ID && ! current_user_can( 'edit_posts' )) {
            $this->response->setInvalid();
            $this->response->setError( 'auth', false );
            $this->response->setStatus(401);
            $this->response->setMessage( "Sorry, you don't have enough rights." );
        }

        $this->next->handle();
    }

}