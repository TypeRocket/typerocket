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
    private $data = null;

    /**
     * Construct Model based on resource
     */
    public function __construct()
    {
        $reflect = new \ReflectionClass( $this );
        $type    = substr( $reflect->getShortName(), 0, - 5 );
        $suffix  = '';

        if ( ! empty( $type )) {
            $suffix = '_' . $type;
        }

        $this->init();
        $this->fillable = apply_filters( 'tr_model_fillable' . $suffix, $this->fillable, $this );
        $this->guard    = apply_filters( 'tr_model_guard' . $suffix, $this->guard, $this );
        do_action( 'tr_model', $this );
    }

    /**
     * Basic initialization
     *
     * Used on construction in concrete classes
     *
     * @return $this
     */
    protected function init()
    {
        return $this;
    }

    public function setFillableFields( array $fillable )
    {
        $this->fillable = $fillable;

        return $this;
    }

    /**
     * Set Guard
     *
     * Fields that are write protected by default unless fillable
     *
     * @param array $guard
     *
     * @return $this
     */
    public function setGuardFields( array $guard )
    {
        $this->guard = $guard;

        return $this;
    }

    /**
     * Append Fillable
     *
     * Add a fillable field.
     *
     * @param $field_name
     *
     * @return $this
     */
    public function appendFillableField( $field_name )
    {
        if ( ! array_key_exists( $field_name, $this->fillable )) {
            $this->fillable[] = $field_name;
        }

        return $this;
    }

    /**
     * Append Guard
     *
     * Add a field to guard.
     *
     * @param $field_name
     *
     * @return $this
     */
    public function appendGuardField( $field_name )
    {
        if ( ! array_key_exists( $field_name, $this->fillable )) {
            $this->guard[] = $field_name;
        }

        return $this;
    }

    /**
     * Resource ID
     *
     * The ID of the resource being used.
     *
     * @return null
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get Errors
     *
     * Get any errors that have been logged
     *
     * @return null
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get Fillable Fields
     *
     * Get all the fields that can be filled
     *
     * @return array|mixed|void
     */
    public function getFillableFields()
    {
        return $this->fillable;
    }

    /**
     * Get Guard Fields
     *
     * Get all the fields that have been write protected
     *
     * @return array|mixed|void
     */
    public function getGuardFields()
    {
        return $this->guard;
    }

    /**
     * Get Builtin Fields
     *
     * Get all the fields that are not saved as meta fields
     *
     * @return array
     */
    public function getBuiltinFields()
    {
        return $this->builtin;
    }

    /**
     * Get Data by key
     *
     * @param $key
     *
     * @return null
     */
    public function getData( $key )
    {
        $data = null;

        if (array_key_exists( $key, $this->data )) {
            $data = $this->data[$key];
        }

        return $data;
    }

    /**
     * Set Data by key
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    protected function setData( $key, $value )
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Get only the fields that are considered to
     * be meta fields.
     *
     * @param array $fields
     *
     * @return array
     */
    protected function getFilteredMetaFields( array $fields )
    {
        $builtin = array_flip( $this->builtin );

        return array_diff_key( $fields, $builtin );
    }

    /**
     * Get only the fields that are considered to
     * be builtin fields.
     *
     * @param array $fields
     *
     * @return array
     */
    protected function getFilteredBuiltinFields( array $fields )
    {
        $builtin = array_flip( $this->builtin );

        return array_intersect_key( $fields, $builtin );
    }

    /**
     * Get fields that have been checked against fillable and guard.
     * Fillable fields override guarded fields.
     *
     * @param array $fields
     *
     * @return mixed|void
     */
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
                if (isset( $fields[$field_name] ) && ! in_array( $field_name, $this->fillable )) {
                    unset( $fields[$field_name] );
                }
            }
        }

        return apply_filters( 'tr_model_secure_fields', $fields, $this );
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
        if ($field instanceof Field) {
            $field = $field->getBrackets();
        }

        if ($this->id == null) {
            return null;
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

    /**
     * Get the value of a field if it is not an empty string or null.
     * If the field is null, undefined or and empty string it will
     * return null.
     *
     * @param $value
     *
     * @return null
     */
    protected function getValueOrNull( $value )
    {
        return ( isset( $value ) && $value !== '' ) ? $value : null;
    }

    /**
     * Create resource by TypeRocket fields
     *
     * When a resource is created the Model ID should be set to the
     * resource's ID.
     *
     * @param array $fields
     *
     * @return mixed
     */
    abstract function create( array $fields );

    /**
     * Update resource by TypeRocket fields
     *
     * @param array $fields
     *
     * @return mixed
     */
    abstract function update( array $fields );

    /**
     * Find resource by ID
     *
     * @param $id
     *
     * @return mixed
     */
    abstract function findById( $id );

    /**
     * Get base field value
     *
     * Some fields need to be saved as serialized arrays. Getting
     * the field by the base value is used by Fields to populate
     * their values.
     *
     * This method must be implemented to return the base value
     * of a field if it is saved as a bracket group.
     *
     * @param $field_name
     *
     * @return null
     */
    abstract protected function getBaseFieldValue( $field_name );

}