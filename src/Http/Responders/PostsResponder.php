<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Middleware\Client,
    \TypeRocket\Http\Middleware\Controller,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response,
    TypeRocket\Registry;

class PostsResponder implements Responder
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
        $request->setMethod('PUT');
        $response = new Response();

        new Controller($request, $response, new Client($request, $response) );

    }

}