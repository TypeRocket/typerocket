<?php
namespace TypeRocket;

class BuilderPlugin
{

    public $post_types = ['page'];

    function __construct()
    {
        $paths = Config::getPaths();
        $path = $paths['urls']['plugins'] . '/builder/';

        $this->post_types = apply_filters('tr_builder_post_types', ['page'] );

        add_action( 'admin_enqueue_scripts', function() use ($path) {
            wp_enqueue_style( 'tr-builder-plugin-css', $path . 'builder.css' );
            wp_enqueue_script( 'tr-builder-plugin-script', $path . 'builder.js', [ 'jquery' ], '1.0', true );
        } );

        do_action('tr_builder_plugin_init', $this);
    }

    function edit_form_after_title( $post )
    {
        if ( is_array( $this->post_types ) && in_array($post->post_type, $this->post_types) ) :

            $form = tr_form();

            $builder_active = $editor_active = '';

            $page_boxes  = tr_posts_field( "builder" );
            $use_builder = tr_posts_field( "use_builder" );
            $is_not_set  = ( ! isset( $use_builder ) || $use_builder === "" );
            $has_boxes   = is_array( $page_boxes );
            $hide_builder = $hide_editor = '';

            if ($use_builder == '1' || ( $has_boxes && $is_not_set )) {
                $builder_active = 'builder-active button-primary ';
                $hide_editor    = 'style="display: none;"';
            } else {
                $editor_active = 'builder-active button-primary ';
                $hide_builder  = 'style="display: none;"';
            }

            echo '<div id="tr_page_type_toggle"><div><a id="tr_page_builder_control" href="#tr_page_builder" class="button ' . $builder_active . '">Builder</a><a href="#builderStandardEditor" class="button ' . $editor_active . '">Standard Editor</a></div></div>';

            echo '<div id="builderSelectRadio">';
            echo $form->checkbox( 'Use Builder' );
            echo '</div>';

            echo '<div id="tr_page_builder" ' . $hide_builder . ' class="typerocket-container typerocket-dev">';
            do_action('tr_before_builder_field', $this, $form, $use_builder);
            echo $form->builder('Builder')->setDebugHelperFunction("tr_posts_components_field('builder');");
            do_action('tr_after_builder_field', $this, $form, $use_builder);
            echo '</div><div id="builderStandardEditor" ' . $hide_editor . '>';

        endif;
    }

    function edit_form_after_editor( $post )
    {
        if ( is_array( $this->post_types ) && in_array($post->post_type, $this->post_types) ) :
            echo '</div>';
        endif;
    }

}

$page_pt = new BuilderPlugin();

add_action( 'edit_form_after_title', [ $page_pt, 'edit_form_after_title' ], 9999999999999 );
add_action( 'edit_form_after_editor', [ $page_pt, 'edit_form_after_editor' ], 0 );
