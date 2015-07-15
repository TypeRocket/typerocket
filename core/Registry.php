<?php
namespace TypeRocket;

class Registry
{

    public static $collection = array();

    public static function add( $obj = null )
    {
        if (is_object( $obj )) {
            array_push(self::$collection, $obj);
        }
    }

    public static function run()
    {
        foreach (self::$collection as $obj) {
            if ($obj instanceof Taxonomy) {
                 add_action( 'init', array( $obj, 'bake' ) );

                // $taxonomy . '_edit_form'
                // $taxonomy . '_add_form_fields'
                // if (isset( $obj->form['bottom'] ) && $obj->form['bottom'] == true) {
                //    add_action( $obj->singular . '_edit_form', array( $obj, 'edit_form_bottom' ) );
                //    add_action( $obj->singular . '_add_form_fields', array( $obj, 'add_form_bottom' ) );
                // }

            } elseif ($obj instanceof PostType) {
                /** @var PostType $obj */
                add_action( 'init', array( $obj, 'bake' ) );

                if (is_string( $obj->getTitlePlaceholder() )) {
                    add_filter( 'enter_title_here', array( $obj, 'enterTitleHere' ) );
                }

                // edit_form_top
                if ($obj->getFrom( 'top' ) === true) {
                    add_action( 'edit_form_top', array( $obj, 'editFormTop' ) );
                } elseif (is_callable( $obj->getFrom( 'top' ) )) {
                    add_action( 'edit_form_top', $obj->getFrom( 'top' ) );
                }

                // edit_form_after_title
                if ($obj->getFrom( 'title' ) === true) {
                    add_action( 'edit_form_after_title', array( $obj, 'editFormAfterTitle' ) );
                } elseif (is_callable( $obj->getFrom( 'title' ) )) {
                    add_action( 'edit_form_after_title', $obj->getFrom( 'title' ) );
                }

                // edit_form_after_editor
                if ($obj->getFrom( 'editor' ) === true) {
                    add_action( 'edit_form_after_editor', array( $obj, 'editFormAfterEditor' ) );
                } elseif (is_callable( $obj->getFrom( 'editor' ) )) {
                    add_action( 'edit_form_after_editor', $obj->getFrom( 'editor' ) );
                }

                // dbx_post_sidebar
                if ($obj->getFrom( 'bottom' ) === true) {
                    add_action( 'dbx_post_sidebar', array( $obj, 'dbxPostSidebar' ) );
                } elseif (is_callable( $obj->getFrom( 'bottom' ) )) {
                    add_action( 'dbx_post_sidebar', $obj->getFrom( 'bottom' ) );
                }

            } elseif ($obj instanceof Metabox) {
                add_action( 'add_meta_boxes', array( $obj, 'bake' ) );
            }
        }
    }
}
