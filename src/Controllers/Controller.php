<?php
namespace TypeRocket\Controllers;

use \TypeRocket\Http\Request,
    \TypeRocket\Http\Response;

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

    /** @var \TypeRocket\Http\Response */
    public $response = null;
    /** @var \TypeRocket\Http\Request */
    public $request = null;
    /** @var \WP_User $user */
    public $user = null;

    function __construct(Request $request, Response $response, \WP_User $user ) {
        $this->response = $response;
        $this->request = $request;
        $this->user = $user;
        $this->validate();
    }

    abstract function update($id);
    abstract function create();
    abstract protected function validate();

}