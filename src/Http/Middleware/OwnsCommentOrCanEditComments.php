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
            $this->response->setError( 'auth', false );
            $this->response->flashNotice( "Sorry, you don't have enough rights.", 'error' );
            $this->response->exit(401);
        }

        $this->next->handle();
    }

}