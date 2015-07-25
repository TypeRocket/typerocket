<?php
namespace TypeRocket\Controllers;

/**
 * Class Controller
 *
 * Be sure to validate by https://codex.wordpress.org/Roles_and_Capabilities
 * when building your own controllers.
 *
 * @package TypeRocket\Controllers
 */
abstract class Controller
{

    public $item_id = null;
    public $action = null;
    public $fields = null;
    public $fieldsBuiltin = null;
    public $valid = true;
    public $response = array( 'message' => 'Response Message', 'errors' => array() );
    public $request = null;
    /** @var \WP_User */
    public $currentUser = null;
    public $requestType = null;
    private $fillable = true;

    protected function save( $item_id, $action = 'update' )
    {
        $this->fillable = apply_filters( 'tr_controller_fillable', $this->fillable, $this );
        $this->filterFillable();
        $this->fields      = ! empty( $_POST['tr'] ) ? $_POST['tr'] : array();
        $this->fieldsBuiltin      = ! empty( $_POST['_tr_builtin_data'] ) ? $_POST['_tr_builtin_data'] : array();
        $this->item_id     = $item_id;
        $this->action      = $action;
        $this->currentUser = wp_get_current_user();

        do_action('tr_controller_save', $this);

        if(empty($this->fields) && empty($this->fieldsBuiltin)) {
            $this->messageNoFields();
        } elseif ($this->getValidate()) {
            $this->filter();

            if ($this->action === 'update') {
                $this->update();
            } elseif ($this->action === 'create') {
                $this->create();
            }

        }

        return $this;

    }

    public function getValidate()
    {
        return $this->valid = apply_filters( 'tr_controller_validate', $this->valid, $this );
    }

    public function setFillable( $fillable )
    {
        $this->fillable = $fillable;

        return $this;
    }

    public function filterFillable()
    {
        if (is_array( $this->fillable )) {

            $keep = array();
            foreach ($this->fillable as $field) {

                if (isset( $_POST['tr'][$field] ) && ! is_bool($field) ) {
                    $keep[$field] = $_POST['tr'][$field];
                }

            }

            $_POST['tr'] = $keep;
        } elseif ($this->fillable === false) {
            $_POST['tr'] = array();
        }

        return $this;

    }

    function getFillable()
    {
        return $this->fillable;
    }

    /**
     * This is a very basic interface to handle REST requests.
     *
     * @param $request
     *
     * @return array
     *
     * The returned array should include
     *   - message The text to display
     *   - valid A bool value
     *   - redirect The url to redirect to if needed
     *   - errors An array of errors
     */
    function getResponseArrayFromRequest( $request )
    {
        $method        = strtoupper( $request->method );
        $this->item_id = $request->id;
        $this->request = $request;

        switch ($method) {
            case 'PUT' :
                $this->response['message'] = 'Updated';
                $this->save( $request->id, 'update' );
                break;
            case 'POST' :
                $this->response['message'] = 'Created';
                $this->save( $request->id, 'create' );
                break;
        }

        $this->response['redirect'] = $this->response['redirect'] ? $this->response['redirect'] : false;

        $response = array_merge($this->response, array( 'valid' => $this->valid ) );

        return $response;

    }

    function messageNoFields() {
        $this->response['message'] = 'No Data Updated';
        $this->valid = false;
    }

    function filter()
    {
        $this->fields = apply_filters( 'tr_controller_filter', $this->fields, $this );
        return $this;
    }

    protected function update()
    {
        return $this;
    }

    protected function create()
    {
        return $this;
    }

}