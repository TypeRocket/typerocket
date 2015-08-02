<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Kernel,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class UsersResponder implements Responder {

    public function respond( $userId ) {

        $request = new Request('users', 'PUT', $userId);
        $response = new Response();

        new Kernel($request, $response);
    }

}