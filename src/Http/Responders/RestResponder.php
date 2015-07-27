<?php
namespace TypeRocket\Http\Responders;

use \TypeRocket\Controllers\Controller,
    \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

class RestResponder
{

    /**
     * @param $resource
     * @param $id
     */
    public function __construct( $resource, $id )
    {

        $request = new Request($resource, $id, 'TypeRocketApi');
        $response = new Response();
        $resource = ucfirst($resource);

        $class = "\\TypeRocket\\Controllers\\{$resource}Controller";

        if ( class_exists( $class ) ) {
            /** @var Controller $model */
            $controller = new $class();

            if ( $controller instanceof Controller ) {
                $response = $controller->getResponseArrayFromRequest( $request, $response );
            }
        }

        status_header( $response->getStatus() );
        wp_send_json( $response->getResponseArray() );
    }

}
