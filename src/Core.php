<?php
namespace TypeRocket;

use TypeRocket\Http\Cookie,
    TypeRocket\Http\Rewrites\Builder,
    TypeRocket\Http\Rewrites\Matrix,
    TypeRocket\Http\Rewrites\Rest,
    TypeRocket\Http\Routes,
    TypeRocket\Layout\Notice;

class Core
{

    /**
     * Core
     *
     * Only initialize core once
     *
     * @param bool|true $init initialize core
     */
    public function __construct($init = false) {
        if($init) {
            new Config();
            require __DIR__ . '/../functions.php';
            $this->initCore();
        }
    }

    /**
     * Core Init
     */
    public function initCore()
    {
        $this->initHooks();
        $this->loadPlugins( new Plugin\PluginCollection() );
        $this->loadResponders();

        /*
        |--------------------------------------------------------------------------
        | Run Registry
        |--------------------------------------------------------------------------
        |
        | Runs after hooks muplugins_loaded, plugins_loaded and setup_theme
        | This allows the registry to work outside of the themes folder. Use
        | the typerocket_loaded hook to access TypeRocket from your WP plugins.
        |
        */
        add_action( 'after_setup_theme', function () {
            do_action( 'typerocket_loaded' );
            Registry::initHooks();
        } );

        /*
        |--------------------------------------------------------------------------
        | Router
        |--------------------------------------------------------------------------
        |
        | Load TypeRocket Router
        |
        */
        $tr_routes_file = realpath( TR_APP_FOLDER_PATH . '/Http/routes.php' );
        if( file_exists($tr_routes_file) ) {
            $tr_routes_vars = require( $tr_routes_file );
            new Http\Routes( $tr_routes_vars );
        }

        $this->initEndpoints();
    }

    /**
     * Admin Init
     */
    private function initHooks()
    {
        $useContent = function($user) {
            echo '<div class="typerocket-container typerocket-wp-style-guide">';
            do_action( 'tr_user_profile', $user );
            echo '</div>';
        };

        add_action( 'post_updated_messages', [$this, 'setMessages']);
        add_action( 'edit_user_profile', $useContent );
        add_action( 'show_user_profile', $useContent );
        add_action( 'admin_init', [$this, 'addCss']);
        add_action( 'admin_init', [$this, 'addJs']);
        add_action( 'admin_footer', [$this, 'addBottomJs']);
        add_action( 'admin_notices', [$this, 'setFlash']);
    }

    /**
     * Front End Init
     */
    public function initFrontEnd()
    {
        Config::enableFrontend();
        add_action( 'wp_enqueue_scripts', [ $this, 'addCss' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'addJs' ] );
        add_action( 'wp_footer', [ $this, 'addBottomJs' ] );
    }

    /**
     * Load plugins
     *
     * @param Plugin\PluginCollection $collection
     */
    public function loadPlugins( Plugin\PluginCollection $collection )
    {
        if (Config::getPlugins()) {
            $plugins = Config::getPlugins();

            foreach ($plugins as $plugin) {
                $collection->append( $plugin );
            }

            $loader = new Plugin\Loader( $collection );
            $loader->load();
        }
    }

    /**
     * Init Responders
     *
     * Add hook into WordPress to give the main functionality needed for
     * TypeRocket to work.
     */
    private function loadResponders() {
        add_action( 'save_post', 'TypeRocket\Http\Responders\Hook::posts' );
        add_action( 'wp_insert_comment', 'TypeRocket\Http\Responders\Hook::comments' );
        add_action( 'edit_comment', 'TypeRocket\Http\Responders\Hook::comments' );
        add_action( 'edit_term', 'TypeRocket\Http\Responders\Hook::taxonomies', 10, 4 );
        add_action( 'create_term', 'TypeRocket\Http\Responders\Hook::taxonomies', 10, 4 );
        add_action( 'profile_update', 'TypeRocket\Http\Responders\Hook::users' );
        add_action( 'user_register', 'TypeRocket\Http\Responders\Hook::users' );
    }

    /**
     * Set custom post type messages to make more since.
     *
     * @param $messages
     *
     * @return mixed
     */
    public function setMessages( $messages )
    {
        global $post;

        $pt = get_post_type( $post->ID );

        if ($pt != 'attachment' && $pt != 'page' && $pt != 'post') :

            $obj      = get_post_type_object( $pt );
            $singular = $obj->labels->singular_name;

            if ($obj->public == true) :
                $view    = sprintf( __( '<a href="%s">View %s</a>' ), esc_url( get_permalink( $post->ID ) ),
                    $singular );
                $preview = sprintf( __( '<a target="_blank" href="%s">Preview %s</a>' ),
                    esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ), $singular );
            else :
                $view = $preview = '';
            endif;

            $messages[$pt] = [
                1  => sprintf( __( '%s updated. %s' ), $singular, $view ),
                2  => __( 'Custom field updated.' ),
                3  => __( 'Custom field deleted.' ),
                4  => sprintf( __( '%s updated.' ), $singular ),
                5  => isset( $_GET['revision'] ) ? sprintf( __( '%s restored to revision from %s' ), $singular,
                    wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
                6  => sprintf( __( '%s published. %s' ), $singular, $view ),
                7  => sprintf( __( '%s saved.' ), $singular ),
                8  => sprintf( __( '%s submitted. %s' ), $singular, $preview ),
                9  => sprintf( __( '%s scheduled for: <strong>%1$s</strong>. %s' ), $singular,
                    date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ) ), $preview ),
                10 => sprintf( __( '%s draft updated. ' ), $singular ),
            ];

        endif;

        return $messages;
    }

    /**
     *  Set flashing for admin notices
     */
    public function setFlash() {
        if( !empty($_COOKIE['tr_admin_flash']) ) {
            $cookie = new Cookie();
            $data = $cookie->getTransient('tr_admin_flash');
            Notice::dismissible($data);
        }
    }

    /**
     * Add CSS
     */
    public function addCss()
    {
        $paths = Config::getPaths();
        $assets = $paths['urls']['assets'];

        wp_enqueue_style( 'typerocket-styles', $assets . '/css/typerocket.css' );

        if (is_admin()) {
            wp_enqueue_style( 'wp-color-picker' );
        }
    }

    /**
     * Add JavaScript
     */
    public function addJs()
    {
        $paths = Config::getPaths();
        $assets = $paths['urls']['assets'];

        wp_enqueue_script( 'typerocket-scripts-global', $assets . '/js/global.js', [], '1.0' );
    }

    /**
     * Add JavaScript to very bottom
     *
     * This is in place so that all other scripts from fields come
     * before the main typerocket script.
     */
    public function addBottomJs()
    {
        $paths = Config::getPaths();
        $assets = $paths['urls']['assets'];

        wp_enqueue_script( 'typerocket-scripts', $assets . '/js/typerocket.js', [ 'jquery' ], '1.0', true );
    }

    /**
     * Endpoints Init
     */
    public function initEndpoints()
    {
        add_action('admin_init', function() {
            // Controller API
            $regex = 'typerocket_rest_api/v1/([^/]*)/?$';
            $location = 'index.php?typerocket_rest_controller=$matches[1]';
            add_rewrite_rule( $regex, $location, 'top' );

            $regex = 'typerocket_rest_api/v1/([^/]*)/([^/]*)/?$';
            $location = 'index.php?typerocket_rest_controller=$matches[1]&typerocket_rest_item=$matches[2]';
            add_rewrite_rule( $regex, $location, 'top' );

            // Matrix API
            $regex = 'typerocket_matrix_api/v1/([^/]*)/([^/]*)/([^/]*)/?$';
            $location = 'index.php?typerocket_matrix_group=$matches[1]&typerocket_matrix_type=$matches[2]&typerocket_matrix_folder=$matches[3]';
            add_rewrite_rule( $regex, $location, 'top' );

            // Builder API
            $regex = 'typerocket_builder_api/v1/([^/]*)/([^/]*)/([^/]*)/?$';
            $location = 'index.php?typerocket_builder_group=$matches[1]&typerocket_builder_type=$matches[2]&typerocket_builder_folder=$matches[3]';
            add_rewrite_rule( $regex, $location, 'top' );
        });

        add_action('init', function() {
            // Routes API
            if( !empty( Routes::$routes ) ) {
                (new Routes)->register();
            }
        });

        add_filter( 'query_vars', function($vars) {
            $vars[] = 'typerocket_rest_controller';
            $vars[] = 'typerocket_rest_item';
            $vars[] = 'typerocket_matrix_group';
            $vars[] = 'typerocket_matrix_folder';
            $vars[] = 'typerocket_matrix_type';
            $vars[] = 'typerocket_builder_group';
            $vars[] = 'typerocket_builder_folder';
            $vars[] = 'typerocket_builder_type';
            $vars = array_merge($vars, Routes::$vars );

            return $vars;
        } );

        add_filter( 'template_include', function($template) {

            $resource = get_query_var('typerocket_rest_controller', null);

            $load_template = ($resource);
            $load_template = apply_filters('tr_rest_api_template', $load_template);

            if($load_template) {
                new Rest();
            }

            return $template;
        }, 99 );

        add_filter( 'template_include', function($template) {

            $matrix_group = get_query_var('typerocket_matrix_group', null);
            $matrix_type = get_query_var('typerocket_matrix_type', null);
            $matrix_folder = get_query_var('typerocket_matrix_folder', null);

            $load_template = ($matrix_group && $matrix_type && $matrix_folder);
            $load_template = apply_filters('tr_matrix_api_template', $load_template);

            if($load_template) {
                new Matrix();
                die();
            }

            return $template;
        }, 99 );

        add_filter( 'template_include', function($template) {

            $builder_group = get_query_var('typerocket_builder_group', null);
            $builder_type = get_query_var('typerocket_builder_type', null);
            $builder_folder = get_query_var('typerocket_builder_folder', null);

            $load_template = ($builder_group && $builder_type && $builder_folder );
            $load_template = apply_filters('tr_builder_api_template', $load_template);

            if($load_template) {
                new Builder();
                die();
            }

            return $template;
        }, 99 );

        add_action( 'rest_api_init', function () {
            register_rest_route( 'typerocket/v1', '/search', [
                'methods' => 'GET',
                'callback' => '\\TypeRocket\\WpRestApi::search'
            ]);
        } );
    }

}
