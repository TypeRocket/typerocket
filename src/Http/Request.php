<?php
namespace TypeRocket\Http;

class Request
{

    private $resource = null;
    private $method = null;
    private $id = null;
    private $uri = null;
    private $host = null;
    private $fields = null;
    private $post = null;
    private $get = null;
    private $files = null;

    public function __construct( $resource, $id )
    {

        // set method
        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $method = ( isset( $_SERVER['REQUEST_METHOD'] ) && isset( $_POST['_method'] ) ) ? $_POST['_method'] : $method;

        $this->setResource( $resource );
        $this->setMethod( $method );
        $this->setResourceId( $id );
        $this->uri    = $_SERVER['REQUEST_URI'];
        $this->host   = $_SERVER['HTTP_HOST'];
        $this->fields = ! empty ( $_POST['tr'] ) ? $_POST['tr'] : array();
        $this->post   = ! empty ( $_POST ) ? $_POST : null;
        $this->get    = ! empty ( $_GET ) ? $_GET : null;
        $this->files  = ! empty ( $_FILES ) ? $_FILES : null;

    }

    public function setDataPost( array $post )
    {
        $this->post = $post;

        return $this;
    }

    public function setDataGet( array $get )
    {
        $this->get = $get;

        return $this;
    }

    public function setDataFiles( array $files )
    {
        $this->files = $files;

        return $this;
    }

    public function setFields( array $fields )
    {
        $this->fields = $fields;

        return $this;
    }

    public function setMethod( $method )
    {
        $this->method = strtoupper( $method );

        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setResource( $resource )
    {
        $this->resource = ucfirst( $resource );

        return $this;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function setResourceId( $id )
    {
        $this->id = $id;

        return $this;
    }

    public function getResourceId()
    {
        return $this->id;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getDataPost()
    {
        return $this->post;
    }

    public function getDataGet()
    {
        return $this->get;
    }

    public function getDataFiles()
    {
        return $this->get;
    }

    public function getFields()
    {
        return $this->fields;
    }

}