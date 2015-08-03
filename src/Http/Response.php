<?php
namespace TypeRocket\Http;

/**
 * Class Response
 *
 * The Response class is not designed for PSR-7
 *
 * This class is designed to give hooks for the json
 * response sent back by the TypeRocket AJAX REST
 * API.
 *
 * @package TypeRocket\Http
 */
class Response {

    private $message = 'No Response';
    private $redirect = false;
    private $status = 200;
    private $valid = true;
    private $flash = true;
    private $errors = array();
    private $data = array();

    /**
     * Set HTTP status code
     *
     * @param $status
     */
    public function setStatus( $status )
    {
        $this->status = (int) $status;
    }

    /**
     * Set message property
     *
     * This is the message seen in the flash alert.
     *
     * @param $message
     *
     * @return $this
     */
    public function setMessage( $message )
    {
        $this->message = (string) $message;

        return $this;
    }

    /**
     * Set redirect
     *
     * Redirect the user to a new url. This only works when using AJAX
     * REST API on a Form.
     *
     * @param $url
     *
     * @return $this
     */
    public function setRedirect( $url )
    {
        $this->redirect = $url;

        return $this;
    }

    /**
     * Set Invalid
     *
     * Set the response as invalid. This does not return a 404 status
     * or a 401 status. If is only a hook to check if a request
     * is valid.
     *
     * @return $this
     */
    public function setInvalid()
    {
        $this->valid = false;

        return $this;
    }

    /**
     * Set Flash
     *
     * Set if the flash message should be shown on the front end.
     *
     * @param bool|true $flash
     *
     * @return $this
     */
    public function setFlash( $flash = true )
    {
        $this->flash = (bool) $flash;

        return $this;
    }

    /**
     * Set Errors
     *
     * Set errors to help front-end developers
     *
     * @param array $errors
     *
     * @return $this
     */
    public function setErrors( array $errors )
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Get Errors
     *
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Get Error by key
     *
     * @param $key
     *
     * @return array
     */
    public function getError($key) {

        $error = null;

        if(array_key_exists($key, $this->errors)) {
            $error = $this->errors[$key];
        }

        return $error;
    }

    /**
     * Set Error by key
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setError($key, $value) {
        $this->errors[$key] = $value;

        return $this;
    }

    /**
     * Remove Error by key
     *
     * @param $key
     *
     * @return $this
     */
    public function removeError($key) {

        if(array_key_exists($key, $this->errors)) {
            unset($this->errors[$key]);
        }

        return $this;

    }

    /**
     * Set Data by key
     *
     * Set the data to return for front-end developers. This should
     * be data used to describe what was updated or created for
     * example.
     *
     * @param $key
     * @param $data
     */
    public function setData( $key, $data ) {
        $this->data[$key] = $data;
    }

    /**
     * Get HTTP status
     *
     * @return int
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Get message
     *
     * Get the message used in the flash alert on front-end
     *
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Get Data
     *
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Get Redirect
     *
     * Get redirect URL used by the AJAX REST API
     *
     * @return bool
     */
    public function getRedirect() {
        return $this->redirect;
    }

    /**
     * Get Valid
     *
     * Get the valid property to see if the response is
     * valid.
     *
     * @return bool
     */
    public function getValid() {
        return $this->valid;
    }

    /**
     * Get Flash
     *
     * Get the flash property to see if the front-end should
     * flash the message.
     *
     * @return bool
     */
    public function getFlash() {
        return $this->flash;
    }

    /**
     * Get Response Properties
     *
     * Return the private properties that make up the response
     *
     * @return array
     */
    public function getResponseArray() {
        $vars = get_object_vars($this);
        return $vars;
    }

}