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

    /**
     * Construct the request
     *
     * @param string $resource the resource
     * @param string $method the method PUT, POST, GET, DELETE
     * @param int $id the resource ID
     */
    public function __construct( $resource, $method, $id )
    {
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

    /**
     * Set the method
     *
     * @param $method
     *
     * @return $this
     */
    private function setMethod( $method )
    {
        $this->method = strtoupper( $method );

        return $this;
    }

    /**
     * Get the method
     *
     * @return null
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set the resource
     *
     * @param $resource
     *
     * @return $this
     */
    private function setResource( $resource )
    {
        $this->resource = ucfirst( $resource );

        return $this;
    }

    /**
     * Get the resource
     *
     * @return null
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set the resource ID
     *
     * @param $id
     *
     * @return $this
     */
    private function setResourceId( $id )
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the Resource ID
     *
     * @return null
     */
    public function getResourceId()
    {
        return $this->id;
    }

    /**
     * Get the request URI
     *
     * @return null
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Get the host
     *
     * @return null
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Get the $_POST data
     *
     * @return null
     */
    public function getDataPost()
    {
        return $this->post;
    }

    /**
     * Get the $_GET data
     *
     * @return null
     */
    public function getDataGet()
    {
        return $this->get;
    }

    /**
     * Get the $_POST files
     *
     * @return null
     */
    public function getDataFiles()
    {
        return $this->get;
    }

    /**
     * Get the fields
     *
     * @return array|null
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set the fields
     *
     * @param array $fields
     *
     * @return array
     */
    public function setFields( array $fields)
    {
        return $this->fields = $fields;
    }

}