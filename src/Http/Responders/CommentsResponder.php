<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Kernel,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class CommentsResponder {

    public function respond( $comment_id ) {

        $request = new Request('comments', $comment_id, 'UsersResponder');
        $response = new Response();

        new Kernel('update', $request, $response);

    }

}