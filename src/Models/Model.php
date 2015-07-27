<?php
namespace TypeRocket\Models;

abstract class Model
{

    protected $fillable = array();
    protected $guard = array();
    protected $errors = null;
    protected $builtin = array();

    public function __construct()
    {
        $this->fillable = apply_filters( 'tr_fillable', $this->fillable, $this );
        $this->guard    = apply_filters( 'tr_guard', $this->guard, $this );
        do_action( 'tr_model' , $this );
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
                if (isset( $fields[$field_name] )) {
                    unset( $fields[$field_name] );
                }
            }
        }

        return $fields;

    }

    abstract function create( array $fields );

    abstract function update( $itemId, array $fields );

}