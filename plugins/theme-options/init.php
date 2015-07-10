<?php
namespace TypeRocket;

use \TypeRocket\Config as Config;

class ThemeOptionsPlugin
{

    public $name = 'tr_theme_options';

    function make()
    {
        add_action( 'admin_menu', array( $this, 'menu' ) );
        add_action( 'wp_before_admin_bar_render', array( $this, 'admin_bar_menu' ), 100 );
        add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts_styles' ) );

        // process import and export
        if (count( $_POST ) < 1 && $_GET['theme-options'] == 'export') {
            $this->json_export();
        } elseif ( ! empty( $_POST['tr_theme_options_import'] )) {
            $files = $_FILES;
            $this->json_import( $files );
        }

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

        $sql     = "SELECT * FROM {$wpdb->options} WHERE option_name = '{$this->name}';";
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

    public function add_scripts_styles()
    {
        if (is_admin()) {
            $paths = Config::getPaths();

            wp_enqueue_script( 'tr_thop-script', $paths['urls']['plugins'] . '/theme-options/js.js',
                array( 'jquery' ), '1.0', true );
            wp_enqueue_style( 'tr_thop-style', $paths['urls']['plugins'] . '/theme-options/css.css' );
        }
    }

}

$tr_theme_options = new ThemeOptionsPlugin();
$tr_theme_options->make();
unset( $tr_theme_options );