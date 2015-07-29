<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Kernel,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response,
    \TypeRocket\Inflect;

class PostsResponder
{

    public function respond( $postId )
    {
        if ( ! $id = wp_is_post_revision( $postId ) ) {
            $id = $postId;
        }

        $type       = get_post_type( $id );
        $type       = Inflect::pluralize( $type );
        $prefix     = ucfirst( $type );
        $controller = "\\TypeRocket\\Controllers\\{$prefix}Controller";
        $model      = "\\TypeRocket\\Controllers\\{$prefix}Model";

        if ( ! class_exists( $controller ) && ! class_exists( $model )) {
            $type = 'posts';
        }

        $request  = new Request( $type, $postId, 'PostsResponder' );
        $response = new Response();

        new Kernel( 'update', $request, $response );

    }

}