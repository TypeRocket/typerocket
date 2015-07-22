<?php
namespace TypeRocket;

class Registry
{

    public static $collection = array();

    /**
     * Add Registrable objects to collection
     *
     * @param null|Registrable|string $obj
     */
    public static function add( $obj = null )
    {
        if ( $obj instanceof Registrable) {
            self::$collection[] = $obj;
        }
    }

    /**
     * Loop through each Registrable and add hooks automatically
     */
    public static function run()
    {
        $collection = self::$collection;

        foreach ($collection as $obj) {
            if ($obj instanceof Taxonomy) {
                add_action( 'init', array( $obj, 'register' ) );
            } elseif ($obj instanceof PostType) {
                /** @var PostType $obj */
                add_action( 'init', array( $obj, 'register' ) );

                if (is_string( $obj->getTitlePlaceholder() )) {
                    add_filter( 'enter_title_here', array( $obj, 'enterTitleHere' ) );
                }

                // edit_form_top
                if ($obj->getForm( 'top' )) {
                    add_action( 'edit_form_top', array( $obj, 'editFormTop' ) );
                }

                // edit_form_after_title
                if ($obj->getForm( 'title' )) {
                    add_action( 'edit_form_after_title', array( $obj, 'editFormAfterTitle' ) );
                }

                // edit_form_after_editor
                if ($obj->getForm( 'editor' )) {
                    add_action( 'edit_form_after_editor', array( $obj, 'editFormAfterEditor' ) );
                }

                // dbx_post_sidebar
                if ($obj->getForm( 'bottom' )) {
                    add_action( 'dbx_post_sidebar', array( $obj, 'dbxPostSidebar' ) );
                }

            } elseif ($obj instanceof Metabox) {
                add_action( 'add_meta_boxes', array( $obj, 'register' ) );
            }
        }
    }
}
