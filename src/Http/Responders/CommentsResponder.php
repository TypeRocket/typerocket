<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class CommentsResponder extends Responder {

    public function respond( $commentId ) {

        $request = new Request('comments', 'PUT', $commentId);
        $response = new Response();

        $this->runKernel($request, $response);

    }

}