<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Kernel,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class CommentsResponder {

    public function respond( $commentId ) {

        $request = new Request('comments', $commentId, 'CommentsResponder');
        $response = new Response();

        new Kernel('update', $request, $response);

    }

}