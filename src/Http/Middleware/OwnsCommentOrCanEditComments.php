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
        $item_id = $this->request->getResourceId();
        $comment = get_comment( $item_id );

        if ( empty($comment->user_id) || ( ! empty($comment->user_id) && $comment->user_id != $currentUser->ID && ! current_user_can( 'edit_comment' ) ) ) {
            $this->response->setError( 'auth', false );
            $this->response->flashNow( "Sorry, you don't have enough rights.", 'error' );
            $this->response->exit(401);
        }

        $this->next->handle();
    }

}