<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Kernel,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response,
    \TypeRocket\Inflect;

class PostsResponder {

    public function respond( $post_id  ) {

        $type = get_post_type($post_id);
        $type = Inflect::pluralize($type);
        $prefix = ucfirst($type);
        $controller = "\\TypeRocket\\Controllers\\{$prefix}Controller";

        if( ! class_exists($controller)) {
            $type = 'posts';
        }

        $request = new Request($type, $post_id, 'PostsResponder');
        $response = new Response();

        new Kernel('update', $request, $response);

    }

}