<?php
namespace TypeRocket\Models;

abstract class Model
{

    protected $id = null;
    protected $fillable = array();
    protected $guard = array();
    protected $errors = null;
    protected $builtin = array();
    protected $data = null;

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
        $this->fillable = apply_filters( 'tr_model_fillable', $this->fillable, $this );
        $this->guard    = apply_filters( 'tr_model_guard', $this->guard, $this );
        do_action( 'tr_model', $this );

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

    abstract function create( array $fields );

    abstract function findById( $id );

    abstract function update( array $fields );

}