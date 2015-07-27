<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Kernel,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class PostsResponder {

    public function respond( $post_id ) {

        $request = new Request('posts', $post_id, 'PostsResponder');
        $response = new Response();

        new Kernel('update', $request, $response);

    }

}