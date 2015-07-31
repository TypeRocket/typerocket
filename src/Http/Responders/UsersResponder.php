<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Middleware\Client,
    \TypeRocket\Http\Middleware\Controller,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class UsersResponder implements Responder {

    public function respond( $userId ) {

        $request = new Request('users', $userId, 'UsersResponder');
        $request->setMethod('PUT');
        $response = new Response();

        new Controller($request, $response, new Client($request, $response) );

    }

}