<?php
namespace TypeRocket\Api;

use \TypeRocket\Controllers\Controller as Controller;

class RestApi
{

    public function __construct( $resource, $id, $method )
    {

        $request = (object) array(
            'resource'    => ucfirst( $resource ),
            'method'      => strtoupper( $method ),
            'id'          => $id,
            'request_uri' => $_SERVER['REQUEST_URI'],
            'host'        => $_SERVER['HTTP_HOST']
        );

        $class = "\\TypeRocket\\Controllers\\{$request->resource}Controller";

        if ( class_exists( $class ) ) {
            /** @var Controller $model */
            $controller = new $class();

            if ( $controller instanceof Controller ) {
                $controller->requestType = 'TypeRocketApi';
                $data                    = $controller->getResponseArrayFromRequest( $request );
                $this->render( $data );
            }
        }

    }

    public function render( array $data )
    {
        wp_send_json( $data );
    }
}
