<?php
namespace TypeRocket\Http;

use \TypeRocket\Controllers\Controller;

class Kernel
{

    public function __construct( $action, Request $request, Response $response )
    {
        do_action('tr_kernel_before', $response, $request, $action);
        $this->handle( $action, $request, $response);
        do_action('tr_kernel_after', $response, $request, $action);
    }

    public function handle( $action, Request $request, Response $response )
    {

        $resource = ucfirst( $request->getResource() );
        $controller    = "\\TypeRocket\\Controllers\\{$resource}Controller";
        $model    = "\\TypeRocket\\Models\\{$resource}Model";

        if ($response->getValid() && class_exists( $controller ) && class_exists( $model ) ) {

            $user = wp_get_current_user();
            $controller = new $controller( $request, $response, $user);
            $id         = $request->getResourceId();

            if ($controller instanceof Controller && $response->getValid()) {
                if (method_exists( $controller, $action )) {
                    $controller->$action( $id );
                } else {
                    $response->setError( 'controller', 'There is no action: ' . $action );
                    $response->setValid( false );
                }
            }

        }

    }

}