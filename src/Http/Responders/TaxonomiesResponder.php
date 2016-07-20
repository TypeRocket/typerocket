<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Http\Request,
    \TypeRocket\Http\Response,
    \TypeRocket\Registry;

class TaxonomiesResponder extends Responder
{

    public $taxonomy = null;

    /**
     * Respond to posts hook
     *
     * Detect the post types registered resource and run the Kernel
     * against that resource.
     *
     * @param $id
     */
    public function respond( $id )
    {
        $taxonomy   = $this->taxonomy;
        $resource   = Registry::getTaxonomyResource( $taxonomy );
        $prefix     = ucfirst( $resource );
        $controller = "\\TypeRocket\\Controllers\\{$prefix}Controller";
        $model      = "\\TypeRocket\\Models\\{$prefix}Model";

        if( ! class_exists( $controller ) || ! class_exists( $model ) ) {
            $controller = "\\" . TR_APP_NAMESPACE . "\\Controllers\\{$prefix}Controller";
            $model      = "\\" . TR_APP_NAMESPACE . "\\Models\\{$prefix}Model";
        }

        if ( empty($prefix) || ! class_exists( $controller ) || ! class_exists( $model )) {
            $resource = 'categories';
        }

        $request  = new Request( $resource, 'PUT', $id, 'update' );
        $response = new Response();

        $this->runKernel($request, $response);

    }

}