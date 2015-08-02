<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class UsersResponder extends Responder {

    public function respond( $userId ) {

        $request = new Request('users', 'PUT', $userId);
        $response = new Response();

        $this->runKernel($request, $response);
    }

}