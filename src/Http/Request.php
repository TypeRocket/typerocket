<?php
namespace TypeRocket\Http;

class Request
{

    private $resource = null;
    private $action = null;
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
     * @param string $action
     */
    public function __construct( $resource = null, $method = null, $id = null, $action = 'auto' )
    {
        $this->resource = $resource;
        $this->method = $method ? $method : $this->getFormMethod();
        $this->id = $id;
        $this->action = $action;
        $this->uri    = $_SERVER['REQUEST_URI'];
        $this->host   = $_SERVER['HTTP_HOST'];
        $this->fields = ! empty ( $_POST['tr'] ) ? $_POST['tr'] : [];
        $this->post   = ! empty ( $_POST ) ? $_POST : null;
        $this->get    = ! empty ( $_GET ) ? $_GET : null;
        $this->files  = ! empty ( $_FILES ) ? $_FILES : null;
    }

    /**
     * Set the method
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
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
     * Get the form method
     *
     * @return string POST|DELETE|PUT|GET
     */
    public function getFormMethod()
    {
        $method = isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        return ( isset( $_POST['_method'] ) ) ? $_POST['_method'] : $method;
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