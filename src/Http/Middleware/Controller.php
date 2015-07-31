<?php
namespace TypeRocket\Http\Middleware;

class Controller extends Middleware
{

    public function handle()
    {
        $method = $this->request->getMethod();
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

        $resource = ucfirst( $this->request->getResource() );
        $controller    = "\\TypeRocket\\Controllers\\{$resource}Controller";
        $model    = "\\TypeRocket\\Models\\{$resource}Model";

        if ($this->response->getValid() && class_exists( $controller ) && class_exists( $model ) ) {
            $user = wp_get_current_user();
            $controller = new $controller( $this->request, $this->response, $user);
            $id         = $this->request->getResourceId();

            if ($controller instanceof \TypeRocket\Controllers\Controller && $this->response->getValid()) {
                if (method_exists( $controller, $action )) {
                    $controller->$action( $id );
                } else {
                    $this->response->setError( 'controller', 'There is no action: ' . $action );
                    $this->response->setValid( false );
                }
            }

        }

        $this->next->handle();
    }

}