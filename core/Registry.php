<?php
namespace TypeRocket;

class Registry
{

    public static $obj = array();

    public static function add( $obj = null )
    {
        if (is_object( $obj )) {
            self::$obj = array_merge(self::$obj, array( $obj ));
        }
    }

    public static function run()
    {
        foreach (self::$obj as $v) {
            if ($v instanceof Taxonomy) {
                 add_action( 'init', array( $v, 'bake' ) );

                // $taxonomy . '_edit_form'
                // $taxonomy . '_add_form_fields'
                // if (isset( $v->form['bottom'] ) && $v->form['bottom'] == true) {
                //    add_action( $v->singular . '_edit_form', array( $v, 'edit_form_bottom' ) );
                //    add_action( $v->singular . '_add_form_fields', array( $v, 'add_form_bottom' ) );
                // }

            } elseif ($v instanceof PostType) {
                /** @var PostType $v */
                add_action( 'init', array( $v, 'bake' ) );

                if (is_string( $v->getTitlePlaceholder() )) {
                    add_filter( 'enter_title_here', array( $v, 'enterTitleHere' ) );
                }

                // edit_form_top
                if ($v->getFrom( 'top' ) === true) {
                    add_action( 'edit_form_top', array( $v, 'editFormTop' ) );
                } elseif (is_callable( $v->getFrom( 'top' ) )) {
                    add_action( 'edit_form_top', $v->getFrom( 'top' ) );
                }

                // edit_form_after_title
                if ($v->getFrom( 'title' ) === true) {
                    add_action( 'edit_form_after_title', array( $v, 'editFormAfterTitle' ) );
                } elseif (is_callable( $v->getFrom( 'title' ) )) {
                    add_action( 'edit_form_after_title', $v->getFrom( 'title' ) );
                }

                // edit_form_after_editor
                if ($v->getFrom( 'editor' ) === true) {
                    add_action( 'edit_form_after_editor', array( $v, 'editFormAfterEditor' ) );
                } elseif (is_callable( $v->getFrom( 'editor' ) )) {
                    add_action( 'edit_form_after_editor', $v->getFrom( 'editor' ) );
                }

                // dbx_post_sidebar
                if ($v->getFrom( 'bottom' ) === true) {
                    add_action( 'dbx_post_sidebar', array( $v, 'dbxPostSidebar' ) );
                } elseif (is_callable( $v->getFrom( 'bottom' ) )) {
                    add_action( 'dbx_post_sidebar', $v->getFrom( 'bottom' ) );
                }

            } elseif ($v instanceof Metabox) {
                add_action( 'add_meta_boxes', array( $v, 'bake' ) );
            }
        }
    }
}