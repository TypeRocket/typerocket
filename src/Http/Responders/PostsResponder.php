<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Request,
    \TypeRocket\Http\Response,
    \TypeRocket\Registry;

class PostsResponder extends Responder
{

    /**
     * Respond to posts hook
     *
     * Detect the post types registered resource and run the Kernel
     * against that resource.
     *
     * @param $postId
     */
    public function respond( $postId )
    {
        if ( ! $id = wp_is_post_revision( $postId ) ) {
            $id = $postId;
        }

        $type       = get_post_type( $id );
        $resource   = Registry::getPostTypeResource( $type );
        $prefix     = ucfirst( $resource );
        $controller = "\\TypeRocket\\Controllers\\{$prefix}Controller";
        $model      = "\\TypeRocket\\Models\\{$prefix}Model";

        if ( empty($prefix) || ! class_exists( $controller ) || ! class_exists( $model )) {
            $resource = 'posts';
        }

        $request  = new Request( $resource, 'PUT', $postId );
        $response = new Response();

        $this->runKernel($request, $response);

    }

}