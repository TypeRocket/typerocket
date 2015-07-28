<?php
namespace TypeRocket\Models;

use TypeRocket\Fields\Field;

abstract class Model
{

    protected $id = null;
    protected $fillable = array();
    protected $guard = array();
    protected $static = array();
    protected $default = array();
    protected $errors = null;
    protected $builtin = array();
    protected $data = null;
    protected $resource = null;

    public function __construct() {

        $reflect = new \ReflectionClass($this);
        $type = substr($reflect->getShortName(),0, -5);
        $this->resource = strtolower($type);
        $suffix = '';

        if(!empty($this->resource)) {
            $suffix = '_' . $this->resource;
        }

        $this->fillable = apply_filters( 'tr_model_fillable' . $suffix, $this->fillable, $this );
        $this->guard    = apply_filters( 'tr_model_guard' . $suffix, $this->guard, $this );
        do_action( 'tr_model', $this );
    }

    public function setFillableFields( array $fillable )
    {
        $this->fillable = $fillable;

        return $this;
    }

    public function setGuardFields( array $guard )
    {
        $this->guard = $guard;

        return $this;
    }

    public function appendFillableField( $field_name )
    {
        if ( ! array_key_exists( $field_name, $this->fillable )) {
            $this->fillable[] = $field_name;
        }

        return $this;
    }

    public function appendGuardField( $field_name )
    {
        if ( ! array_key_exists( $field_name, $this->fillable )) {
            $this->guard[] = $field_name;
        }

        return $this;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getFillableFields()
    {
        return $this->fillable;
    }

    public function getGuardFields()
    {
        return $this->guard;
    }

    public function getData() {
        return $this->data;
    }

    protected function getBuiltin()
    {
        return $this->builtin;
    }

    protected function getMetaFields( array $fields )
    {
        $builtin = array_flip( $this->builtin );

        return array_diff_key( $fields, $builtin );
    }

    protected function getBuiltinFields( array $fields )
    {
        $builtin = array_flip( $this->builtin );

        return array_intersect_key( $fields, $builtin );
    }

    protected function secureFields( array $fields )
    {

        $fillable = array();
        if ( ! empty( $this->fillable ) && is_array( $this->fillable )) {
            foreach ($this->fillable as $field_name) {
                if (isset( $fields[$field_name] )) {
                    $fillable[$field_name] = $fields[$field_name];
                }
            }
            $fields = $fillable;
        }

        if ( ! empty( $this->guard ) && is_array( $this->guard )) {
            foreach ($this->guard as $field_name) {
                if (isset( $fields[$field_name] ) && ! in_array($field_name, $this->fillable)) {
                    unset( $fields[$field_name] );
                }
            }
        }

        return apply_filters( 'tr_model_filter_fields', $fields, $this );

    }

    /**
     * Get value from database from typeRocket bracket syntax
     *
     * @param $field
     *
     * @return array|mixed|null|string
     */
    public function getFieldValue( $field )
    {
        if($field instanceof Field) {
            $field = $field->getBrackets();
        }

        $keys = $this->geBracketKeys( $field );
        $data = $this->getBaseFieldValue( $keys[0] );

        return $this->parseValueData( $data, $keys );
    }

    /**
     * Parse data by walking through keys
     *
     * @param $data
     * @param $keys
     *
     * @return array|mixed|null|string
     */
    private function parseValueData( $data, $keys )
    {
        $mainKey = $keys[0];
        if (isset( $mainKey ) && ! empty( $data )) {

            if (is_serialized( $data )) {
                $data = unserialize( $data );
            }

            // unset first key since $data is already set to it
            unset( $keys[0] );

            if ( ! empty( $keys ) && is_array( $keys )) {
                foreach ($keys as $name) {
                    $data = ( isset( $data[$name] ) && $data[$name] !== '' ) ? $data[$name] : null;
                }
            }

        }

        return $data;
    }

    /**
     * Get keys from TypeRocket brackets
     *
     * @param $str
     * @param int $set
     *
     * @return mixed
     */
    private function geBracketKeys( $str, $set = 1 )
    {
        $regex = '/\[([^]]+)\]/i';
        preg_match_all( $regex, $str, $matches, PREG_PATTERN_ORDER );

        return $matches[$set];
    }

    protected function getValueOrNull($value) {
        return $value !== '' ? $value : null;
    }

    abstract function create( array $fields );

    abstract function findById( $id );

    abstract protected function getBaseFieldValue( $field_name );

    abstract function update( array $fields );

}