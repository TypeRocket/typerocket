<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Middleware\Client,
    \TypeRocket\Http\Middleware\Controller,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class CommentsResponder implements Responder {

    public function respond( $commentId ) {

        $request = new Request('comments', $commentId, 'CommentsResponder');
        $request->setMethod('PUT');
        $response = new Response();

        new Controller($request, $response, new Client($request, $response) );

    }

}