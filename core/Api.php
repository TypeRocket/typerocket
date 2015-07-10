<?php

namespace TypeRocket;

class Api {

    public $resource;
    public $id;
    public $method;
    public $version;

    function init($resource, $id, $method, $version) {

        $this->resource = ucfirst($resource);
        $this->id = $id;
        $this->version = $version;
        $this->method = strtoupper($method);

        $class = "\\TypeRocket\\Controllers\\$resource";
        /** @var Controllers\Controller $model */
        $model = new $class();

        if($model instanceof Controllers\Controller) {
            $data = $model->handleRest($this->id, $this->method);

            if ($data == null) {
                $data = array('api_v' => $this->version);
            }

            $this->render($data);
        }

    }

    function render($data) {
        wp_send_json( $data );
    }
}