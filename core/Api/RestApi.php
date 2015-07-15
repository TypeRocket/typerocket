<?php
namespace TypeRocket\Api;

use \TypeRocket\Controllers\Controller as Controller;

class RestApi {

    public $resource;
    public $id;
    public $method;
    public $version;

    function init($resource, $id, $method, $version) {

        $this->resource = ucfirst($resource);
        $this->id = $id;
        $this->version = $version;
        $this->method = strtoupper($method);

        $class = "\\TypeRocket\\Controllers\\{$this->resource}Controller";

        if(class_exists($class)) {
            /** @var Controller $model */
            $controller = new $class();

            if($controller instanceof Controller) {
                $controller->requestType = 'TypeRocketApi';
                $data = $controller->handleRest($this->id, $this->method);

                if ($data == null) {
                    $data = array('api_v' => $this->version);
                }

                $this->render($data);
            }
        }

    }

    function render($data) {
        wp_send_json( $data );
    }
}
