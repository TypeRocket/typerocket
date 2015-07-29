<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Kernel,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class UsersResponder {

    public function respond( $userId ) {

        $request = new Request('users', $userId, 'UsersResponder');
        $response = new Response();

        new Kernel('update', $request, $response);

    }

}