<?php
namespace TypeRocket\Http\Middleware;

class Controller extends Middleware
{

    public function handle()
    {
        $request = $this->request;
        $response = $this->response;

        $method = $request->getMethod();
        $action = null;
        switch ($method) {
            case 'PUT' :
                $action = 'update';
                break;
            case 'GET' :
                $action = 'read';
                break;
            case 'DELETE' :
                $action = 'delete';
                break;
            case 'POST' :
                $action = 'create';
                break;
        }

        $resource = ucfirst( $request->getResource() );
        $controller    = "\\TypeRocket\\Controllers\\{$resource}Controller";
        $model    = "\\TypeRocket\\Models\\{$resource}Model";

        if ($response->getValid() && class_exists( $controller ) && class_exists( $model ) ) {
            $user = wp_get_current_user();
            $controller = new $controller( $request, $response, $user);
            $id         = $request->getResourceId();

            if ($controller instanceof \TypeRocket\Controllers\Controller && $response->getValid()) {
                if (method_exists( $controller, $action )) {
                    $controller->$action( $id );
                } else {
                    $response->setError( 'controller', 'There is no action: ' . $action );
                    $this->response->setInvalid();
                }
            }

        }

    }

}