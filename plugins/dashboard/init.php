<?php
/**
 * Based on @link https://gist.github.com/wpscholar/4744033
 *
 * Class TypeRocketDashboard
 */
class TypeRocketDashboard {

    protected $capability = 'read';
    protected $page = 'tr-custom-dashboard';
    protected $title = 'Dashboard';


    public function __construct() {
        if( is_admin() ) {
            add_action( 'init', array( $this, 'init' ) );
        }
    }

    /**
     * Init Dashboard
     */
    public function init() {
        $this->capability = apply_filters( 'tr_custom_dashboard_capability', $this->capability );

        if( current_user_can( $this->capability ) ) {
            $this->page = apply_filters( 'tr_custom_dashboard_page', $this->page );
            $this->title = apply_filters( 'tr_custom_dashboard_title', __($this->title) );
            add_filter( 'admin_title', array( $this, 'admin_title' ), 10, 2 );
            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
            add_action( 'current_screen', array( $this, 'current_screen' ) );
        }
    }

    /**
     * Output the content for your custom dashboard
     */
    function page_content() {
        $file = apply_filters( 'tr_custom_dashboard_admin_page', __DIR__ . '/admin.php' );
        echo '<div id="typerocket-custom-dashboard" class="wrap">';
        include $file;
        echo '</div>';
    }

    /**
     * Fixes the page title in the browser.
     *
     * @param string $admin_title
     * @param string $title
     * @return string $admin_title
     */
    public function admin_title( $admin_title, $title ) {
        global $pagenow;
        if( 'admin.php' == $pagenow && isset( $_GET['page'] ) && $this->page == $_GET['page'] ) {
            $admin_title = $this->title . $admin_title;
        }
        return $admin_title;
    }

    public function admin_menu() {
        /**
         * Adds a custom page to WordPress
         */
        add_menu_page( $this->title, '', 'manage_options', $this->page, array( $this, 'page_content' ) );
        /**
         * Remove the custom page from the admin menu
         */
        remove_menu_page('tr-custom-dashboard');
        /**
         * Make dashboard menu item the active item
         */
        global $pagenow, $plugin_page;
        if(in_array($pagenow, ['update-core.php'])) {
            add_filter('parent_file', function($v) { return 'index.php'; }, 9999);
            add_filter('submenu_file',function($v) use ($pagenow) { return $pagenow;  }, 9999);
        } elseif($plugin_page == $this->page) {
            add_filter('parent_file', function($v) { return 'index.php'; }, 9999);
            add_filter('submenu_file',function($v) { return 'index.php'; }, 9999);
        }
        /**
         * Rename the dashboard menu item
         */
        global $menu;
        $menu[2][0] = $this->title;
        /**
         * Rename the dashboard submenu item
         */
        global $submenu;
        $submenu['index.php'][0][0] = $this->title;
    }

    /**
     * Redirect users from the normal dashboard to your custom dashboard
     */
    public function current_screen( $screen ) {
        if( 'dashboard' == $screen->id ) {
            wp_safe_redirect( admin_url('admin.php?page=' . $this->page) );
            exit;
        }
    }
}
new TypeRocketDashboard();