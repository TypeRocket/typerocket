<?php
namespace TypeRocket\Http\Middleware;

/**
 * Class OwnsCommentOrCanEditComments
 *
 * Validate that user owns comment or can edit comments
 * and if the user is not invalidate the response.
 *
 * @package TypeRocket\Http\Middleware
 */
class OwnsCommentOrCanEditComments extends Middleware
{

    public function handle() {

        $currentUser = wp_get_current_user();
        $comment = get_comment( $this->request->getResourceId() );

        if ($comment->user_id != $currentUser->ID && ! current_user_can( 'edit_comment' )) {
            $this->response->setInvalid();
            $this->response->setError( 'auth', false );
            $this->response->setStatus(401);
            $this->response->setMessage( "Sorry, you don't have enough rights." );
        }

        $this->next->handle();
    }

}