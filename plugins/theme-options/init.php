<?php
namespace TypeRocket;

class ThemeOptionsPlugin
{

    private $name = 'tr_theme_options';

    public function __construct()
    {
        if ( ! function_exists( 'add_action' )) {
            echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
            exit;
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function setup()
    {
        $this->name = apply_filters( 'tr_theme_options_name', $this->name );
        add_action( 'admin_menu', array( $this, 'menu' ) );
        add_action( 'wp_before_admin_bar_render', array( $this, 'admin_bar_menu' ), 100 );
        add_filter( 'tr_model', array( $this, 'fillable' ), 9999999999 );

        // process import and export
        if (
            empty( $_POST ) &&
            ! empty($_GET['theme-options']) &&
            ! empty($_GET) &&
            $_GET['theme-options'] == 'export'
        ) {
            $this->json_export();
        } elseif ( ! empty( $_POST['tr_theme_options_import'] )) {
            $files = $_FILES;
            $this->json_import( $files );
        }

    }

    public function fillable( $model )
    {

        if ($model instanceof Models\OptionsModel) {
            $fillable = $model->getFillableFields();

            if ( ! empty( $fillable )) {
                $model->appendFillableField( $this->name );
            }
        }

    }

    public function menu()
    {
        add_theme_page( 'Theme Options', 'Theme Options', 'manage_options', 'theme_options', array( $this, 'page' ) );
    }

    public function page()
    {
        do_action('tr_theme_options_page', $this);
        echo '<div id="wrap">';
        $file = apply_filters( 'tr_theme_options_page', __DIR__ . '/admin.php' );
        if (file_exists( $file )) {
            include( $file );
        }

        include( __DIR__ . '/import.php' );
        echo '</div>';
    }

    public function add_sub_menu( $name, $link, $root_menu, $id, $meta = false )
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

    public function admin_bar_menu()
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

add_action( 'typerocket_loaded', array( new ThemeOptionsPlugin(), 'setup' ) );