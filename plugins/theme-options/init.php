<?php
namespace TypeRocket;

use \TypeRocket\Controllers\OptionsController;

class ThemeOptionsPlugin
{

    private $name = 'tr_theme_options';

    function __construct() {
        if ( !function_exists( 'add_action' ) ) {
            echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
            exit;
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name) {
        $this->name = (string) $name;
    }

    function setup()
    {
        add_action( 'admin_menu', array( $this, 'menu' ) );
        add_action( 'wp_before_admin_bar_render', array( $this, 'admin_bar_menu' ), 100 );
        add_filter('tr_options_controller_fillable', array($this, 'fillable'), 9999999998 );

        // process import and export
        if (count( $_POST ) < 1 && $_GET['theme-options'] == 'export') {
            $this->json_export();
        } elseif ( ! empty( $_POST['tr_theme_options_import'] )) {
            $files = $_FILES;
            $this->json_import( $files );
        }

    }

    public function fillable($fillable) {

        if(is_array($fillable)) {
            $fillable = array_merge($fillable, array($this->name));
        }

        return $fillable;

    }

    public function menu()
    {
        add_theme_page( 'Theme Options', 'Theme Options', 'manage_options', 'theme_options', array( $this, 'page' ) );
    }

    function page()
    {
        echo '<div id="wrap">';
        $file = apply_filters( 'tr_theme_options_admin', __DIR__ . '/admin.php' );
        if (file_exists( $file )) {
            include( $file );
        }
        include( __DIR__ . '/import.php' );
        echo '</div>';
    }

    function add_sub_menu( $name, $link, $root_menu, $id, $meta = false )
    {
        global $wp_admin_bar;
        if ( ! current_user_can( 'manage_options' ) || ! is_admin_bar_showing()) {
            return;
        }

        $wp_admin_bar->add_menu( array(
            'parent' => $root_menu,
            'id'     => $id,
            'title'  => $name,
            'href'   => $link,
            'meta'   => $meta
        ) );
    }

    function admin_bar_menu()
    {
        $this->add_sub_menu( "Theme Options", admin_url() . 'themes.php?page=theme_options', "site-name",
            "tr-theme-options" );
    }

    public function json_export()
    {
        /** @var \wpdb $wpdb */
        global $wpdb;
        $name = esc_sql( $this->name );

        $sql     = "SELECT * FROM {$wpdb->options} WHERE option_name = '{$name}';";
        $arr     = array();
        $options = $wpdb->get_results( $sql );
        if ( ! $options) {
            $arr = array( 'error' => 'no results. check filters. AND?' );
        }
        foreach ($options as $option) {
            $arr[$option->option_name] = unserialize( $option->option_value );
        }

        $json = json_encode( $arr );

        $uploads = wp_upload_dir();

        $fp = fopen( $uploads['basedir'] . '/typerocket-export-theme-options.json', 'w' );
        fwrite( $fp, $json );
        fclose( $fp );

        wp_redirect( $uploads['baseurl'] . '/typerocket-export-theme-options.json' );

    }

    public function json_import( $files )
    {

        if ( ! empty( $files['fileToUpload'] ) && $files['fileToUpload']['type'] == 'application/json') {
            $data = json_decode( file_get_contents( $files['fileToUpload']['tmp_name'] ), true );

            if (is_array( $data )) {
                $theme_options = $data[$this->name];
                update_option( $this->name, serialize( $theme_options ) );
            }
        }
    }

}

$tr_theme_options = new ThemeOptionsPlugin();
add_action( 'typerocket_loaded', array( $tr_theme_options, 'setup' ) );
unset( $tr_theme_options );