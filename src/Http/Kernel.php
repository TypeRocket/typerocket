<?php
namespace TypeRocket\Http;

use TypeRocket\Http\Middleware\Controller;

class Kernel
{

    protected $middleware = array(
        'hookGlobal' =>
            array('AuthRead'),
        'restGlobal' =>
            array(
                'AuthRead',
                'ValidateCsrf'
            ),
        'users' =>
            array('IsUserOrCanEditUsers'),
        'posts' =>
            array('OwnsPostOrCanEditPosts'),
        'pages' =>
            array('OwnsPostOrCanEditPosts'),
        'comments' =>
            array('OwnsCommentOrCanEditComments'),
        'options' =>
            array('CanManageOptions')
    );

    public function __construct(Request $request, Response $response, $type = 'hookGlobal') {

        $resource = strtolower( $request->getResource() );
        $resourceMiddleware = array();

        if(array_key_exists($resource, $this->middleware)) {
            $resourceMiddleware = $this->middleware[$resource];
        }

        $client = new Controller($request, $response);

        $middleware = array_merge($resourceMiddleware, $this->middleware[$type]);
        $middleware = array_reverse($middleware);

        foreach($middleware as $class) {
            $class = '\\TypeRocket\\Http\\Middleware\\' . $class;
            $client = new $class($request, $response, $client);
        }

        $client->handle();

    }

}