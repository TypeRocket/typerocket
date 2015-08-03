<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class UsersResponder extends Responder {

    /**
     * Respond to user hook
     *
     * Create proper request and run through Kernel
     *
     * @param $userId
     */
    public function respond( $userId ) {

        $request = new Request('users', 'PUT', $userId);
        $response = new Response();

        $this->runKernel($request, $response);
    }

}