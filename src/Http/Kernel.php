<?php
namespace TypeRocket\Http;

use \TypeRocket\Controllers\Controller;

class Kernel
{

    public function __construct( $action, Request $request, Response $response )
    {
        $this->handle( $action, $request, $response);
    }

    public function handle( $action, Request $request, Response $response )
    {

        $resource = ucfirst( $request->getResource() );
        $class    = "\\TypeRocket\\Controllers\\{$resource}Controller";

        if ($response->getValid() && class_exists( $class )) {

            if(get_current_user_id()) {
                $user = wp_get_current_user();
            } else {
                $user = null;
            }

            $controller = new $class( $request, $response,  $user);
            $id         = $request->getResourceId();

            if ($controller instanceof Controller && $response->getValid()) {
                if (method_exists( $controller, $action )) {
                    $controller->$action( $id );
                } else {
                    $response->setError( 'action', 'There is no action.' );
                    $response->setValid( false );
                }
            }

        }

    }

}