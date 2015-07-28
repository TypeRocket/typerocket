<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Kernel,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class UsersResponder {

    public function respond( $user_id, $context = null ) {

        $request = new Request('users', $user_id, 'UsersResponder');
        $response = new Response();

        new Kernel('update', $request, $response);

    }

}