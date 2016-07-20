<?php

namespace TypeRocket\Models;

class SchemaModel extends Model
{
    public $resource = null;
    public $table = null;

    protected $query = [];
    public $lastCompiledSQL = null;
    public $return_one = false;

    protected $guard = [
        'id'
    ];

    /**
     * Find all
     *
     * @param array $ids
     *
     * @return $this
     */
    public function findAll( array $ids = [] )
    {
        $this->query['select'] = true;

        if(!empty($ids)) {
            $this->where('id', 'IN', $ids);
        }

        return $this;
    }

    /**
     * Get results from find methods
     *
     * @return array|null|object
     */
    public function get() {
        return $this->runQuery();
    }

    /**
     * Where
     *
     * @param string $column
     * @param string $arg1
     * @param null|string $arg2
     * @param string $condition
     *
     * @return $this
     */
    public function where($column, $arg1, $arg2 = null, $condition = 'AND')
    {
        $whereQuery = [];

        if( !empty($this->query['where']) ) {
            $whereQuery['condition'] = strtoupper($condition);
        } else {
            $whereQuery['condition'] = 'WHERE';
        }

        $whereQuery['column'] = $column;
        $whereQuery['operator'] = '=';
        $whereQuery['value'] = $arg1;

        if( !empty($arg2) ) {
            $whereQuery['operator'] = strtoupper($arg1);
            $whereQuery['value'] = $arg2;
        }

        $this->query['where'][] = $whereQuery;

        return $this;
    }

    /**
     * Or Where
     *
     * @param string $column
     * @param string $arg1
     * @param null|string $arg2
     *
     * @return \TypeRocket\Models\SchemaModel
     */
    public function orWhere($column, $arg1, $arg2 = null)
    {
        return $this->where($column, $arg1, $arg2, 'OR');
    }

    /**
     * Order by
     *
     * @param string $column name of column
     * @param string $direction default ASC other DESC
     *
     * @return $this
     */
    public function orderBy($column = 'id', $direction = 'ASC')
    {
        $this->query['order_by']['column'] = $column;
        $this->query['order_by']['direction'] = $direction;

        return $this;
    }

    /**
     * Take only a select group
     *
     * @param $limit
     *
     * @param int $offset
     *
     * @return $this
     */
    public function take( $limit, $offset = 0 ) {
        $this->query['take']['limit'] = (int) $limit;
        $this->query['take']['offset'] = (int) $offset;

        return $this;
    }

    /**
     * @return array|bool|false|int|null|object
     */
    public function first() {
        $this->return_one = true;
        $this->take(1);
        return $this->runQuery();
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
    public function create(array $fields)
    {
        $fields = $this->secureFields($fields);
        $fields = array_merge($this->default, $fields, $this->static);

        $this->query['create'] = true;
        unset($this->query['select']);
        $this->query['data'] = $fields;

        return $this->runQuery();
    }

    /**
     * Update resource by TypeRocket fields
     *
     * @param array $fields
     *
     * @return mixed
     */
    public function update(array $fields)
    {
        $fields = $this->secureFields($fields);
        $fields = array_merge($this->default, $fields, $this->static);

        $this->query['update'] = true;
        unset($this->query['select']);
        $this->query['data'] = $fields;

        return $this->runQuery();
    }

    /**
     * Find resource by ID
     *
     * @param $id
     *
     * @return $this
     */
    public function findById($id)
    {
        $this->id = (int) $id;
        return $this->where('id', $id)->take(1)->findAll();
    }

    /**
     * Delete
     *
     * @param array $ids
     *
     * @return array|false|int|null|object
     */
    public function delete( array $ids = [] ) {
        $this->query['delete'] = true;
        unset($this->query['select']);

        if(!empty($ids)) {
            $this->where('id', 'IN', $ids);
        }

        return $this->runQuery();
    }

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
    protected function getBaseFieldValue($field_name)
    {
        $data = $this->findById($this->id)->get();
        return $this->getValueOrNull( $data[0]->$field_name );
    }

    /**
     * Run the SQL query from the query property
     *
     * @param array $query
     *
     * @return array|bool|false|int|null|object
     */
    protected function runQuery( array $query = [] ) {
        /** @var \wpdb $wpdb */
        global $wpdb;

        $table = $this->table ? $this->table : $wpdb->prefix . $this->resource;
        $result = [];
        $sql_where = $sql_limit = $sql_values = $sql_columns = $sql_update = $sql = $sql_order = '';

        if( empty($query) ) {
            $query = $this->query;
        }

        // compile where
        if( !empty($query['where']) ) {
            foreach( $query['where'] as $where ) {

                if( is_array($where['value']) ) {

                    $where['value'] = array_map(function($value) use ($wpdb) {
                        return $wpdb->prepare( '%s', $value );
                    }, $where['value']);

                    $where['value'] = '(' . implode(',', $where['value']) . ')';
                } else {
                    $where['value'] = $wpdb->prepare( '%s', $where['value'] );
                }

                $sql_where .= ' ' . implode(' ', $where);
            }
        }

        // compile insert
        if( !empty($query['create']) && !empty($query['data']) ) {
            $inserts = $columns = [];
            foreach( $query['data'] as $column => $data ) {
                $columns[] = preg_replace("/[^a-z0-9\\\\_]+/", '', $column);

                if( is_array($data) ) {
                    $inserts[] = $wpdb->prepare( '%s', json_encode($data) );
                } else {
                    $inserts[] = $wpdb->prepare( '%s', $data );
                }
            }

            $sql_columns = ' (' . implode(',', $columns) . ') ';
            $sql_values .= ' ( ' . implode(',', $inserts) . ' ) ';
        }

        // compile update
        if( !empty($query['update']) && !empty($query['data']) ) {
            $inserts = $columns = [];
            foreach( $query['data'] as $column => $data ) {
                $columns[] = preg_replace("/[^a-z0-9\\\\_]+/", '', $column);

                if( is_array($data) ) {
                    $inserts[] = $wpdb->prepare( '%s', json_encode($data) );
                } else {
                    $inserts[] = $wpdb->prepare( '%s', $data );
                }
            }

            $sql_update = implode(', ', array_map(
                function ($v, $k) { return sprintf("%s=%s", $k, $v); },
                $inserts,
                $columns
            ));
        }

        // compile take
        if( !empty($query['take']) ) {
            $sql_limit .= ' ' . $wpdb->prepare( 'LIMIT %d OFFSET %d', $query['take'] );
        }

        // compile order
        if( !empty($query['order_by']) ) {
            $order_column = preg_replace("/[^a-z0-9\\\\_]+/", '', $query['order_by']['column']);
            $order_direction = $query['order_by']['direction'] == 'ASC' ? 'ASC' : 'DESC';
            $sql_order .= " ORDER BY {$order_column} {$order_direction}";
        }

        if( array_key_exists('select', $query) ) {
            $sql = 'SELECT * FROM '. $table . $sql_where . $sql_order . $sql_limit;
            $result = $wpdb->get_results( $sql );

            if($result && $this->return_one) {
                $result = $result[0];
            }

        } elseif( array_key_exists('delete', $query) ) {
            $sql = 'DELETE FROM ' . $table . $sql_where;
            $result = $wpdb->query( $sql );
        } elseif( array_key_exists('create', $query) ) {
            $sql = 'INSERT INTO ' . $table . $sql_columns . ' VALUES ' . $sql_values;
            $result = false;
            if( $wpdb->query( $sql ) ) {
                $result = $wpdb->insert_id;
            };
        } elseif( array_key_exists('update', $query) ) {
            $sql = 'UPDATE ' . $table . ' SET ' . $sql_update . $sql_where;
            $result = $wpdb->query( $sql );
        }

        $this->lastCompiledSQL = $sql;

        return $result;
    }
}