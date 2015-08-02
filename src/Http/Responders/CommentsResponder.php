<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Kernel,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class CommentsResponder implements Responder {

    public function respond( $commentId ) {

        $request = new Request('comments', 'PUT', $commentId);
        $response = new Response();

        new Kernel($request, $response);

    }

}