<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Kernel,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response,
    TypeRocket\Registry;

class PostsResponder
{

    public function respond( $postId )
    {
        if ( ! $id = wp_is_post_revision( $postId ) ) {
            $id = $postId;
        }

        $type       = get_post_type( $id );
        $type       = Registry::getPostTypeResource( $type );
        $prefix     = ucfirst( $type );
        $controller = "\\TypeRocket\\Controllers\\{$prefix}Controller";
        $model      = "\\TypeRocket\\Models\\{$prefix}Model";

        if ( empty($prefix) || ! class_exists( $controller ) || ! class_exists( $model )) {
            $type = 'posts';
        }

        $request  = new Request( $type, $postId, 'PostsResponder' );
        $response = new Response();

        new Kernel( 'update', $request, $response );

    }

}