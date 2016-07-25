<?php

namespace TypeRocket\Http;

use TypeRocket\Http\Responders\ResourceResponder;
use TypeRocket\View;

/**
 * Class Routes
 *
 * Store all the routes for TypeRocket Resources if there are any.
 *
 * @package TypeRocket\Http
 */
class Routes
{
    public static $routes = [];
    public static $vars = [];

    public function __construct( array $routes = [] )
    {
        if( !empty($routes) ) {
            self::$routes = $routes;
        }
    }

    /**
     * Register the new routes
     */
    public function register() {
        $request = new Request();
        $request_method = $request->getFormMethod();
        foreach( self::$routes as $resource => $routes ) {

            foreach ($routes as $route) {
                $vars = [];
                list($method, $page, $action) = explode(':', $route);
                $method = strtoupper($method);

                // Resource
                $regex    = $resource . '/?$';
                $vars[]   = $var = 'typerocket_' . $resource;
                $location = 'index.php?' . $var . '=/';
                add_rewrite_rule($regex, $location, 'top');

                // Action
                $regex    = $resource . '/([^/]*)/?$';
                $vars[]   = $var = 'typerocket_' . $resource . '_page';
                $location = 'index.php?' . $var . '=$matches[1]';
                add_rewrite_rule($regex, $location, 'top');

                // Item
                $regex    = $resource . '/([^/]*)/([^/]*)/?$';
                $vars[]   = $var = 'typerocket_' . $resource . '_item';
                $location = $location . '&' . $var . '=$matches[2]';
                add_rewrite_rule($regex, $location, 'top');

                self::$vars = array_merge(self::$vars, $vars);

                add_filter('template_include', function ($template) use ($resource, $route, $routes, $request_method, $page, $action, $method, $vars) {
                    global $wp_query;
                    $is_root = array_key_exists($vars[0], $wp_query->query_vars);
                    $is_page = array_key_exists($vars[1], $wp_query->query_vars);

                    if ( $is_root || $is_page ) {
                        $var_page   = get_query_var($vars[1], null );
                        $item_id  = get_query_var($vars[2], null);
                        $end = end($routes) == $route;

                        if( $request_method != $method || $var_page != $page && $is_page ) {

                            if($end) {
                                wp_die('Invalid route');
                            }

                            return $template;
                        }

                        $respond = new ResourceResponder();
                        $respond->setResource( ucfirst($resource) );
                        $respond->setAction( $action );
                        $respond->setActionMethod( strtoupper( $method ) );

                        if ($resource && $is_page ) {
                            $respond->respond($item_id);
                            $returned = $respond->kernel->router->returned;
                            if( $returned instanceof View ) {
                                $template = $returned->template();
                            } else {
                                $template = get_template_directory() . '/resource-' . $resource . '-' . $page . '.php';
                            }
                            $this->getTemplate($template);
                        } elseif ($resource && $is_root ) {
                            $respond->setAction( 'index' );
                            $respond->respond($item_id);
                            $returned = $respond->kernel->router->returned;
                            if( $returned instanceof View ) {
                                $template = $returned->template();
                            } else {
                                $template = get_template_directory() . '/resource-' . $resource . '.php';
                            }
                            $this->getTemplate($template);
                        } else {
                            wp_die('Invalid route');
                        }
                    }

                    return $template;
                }, 99);
            }
        }
    }

    private function getTemplate($template) {
        new View( $template );
        unset($template);
        extract(View::$data);
        include ( View::$file );
        die();
    }

}