<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Controller,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class UsersResponder implements Responder {

    public function respond( $userId ) {

        $request = new Request('users', $userId);
        $request->setMethod('PUT');
        $response = new Response();

        new Controller($request, $response);
    }

}