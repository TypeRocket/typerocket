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
        $collection = array();
        $later = array();

        if(empty(self::$collection)) {
            return;
        }

        foreach(self::$collection as $obj) {
            if ( $obj instanceof Registrable) {
                $collection[] = $obj;
                $use = $obj->getApplied();
                foreach($use as $objUsed) {
                    if( ! in_array($objUsed, $collection)) {
                        $later[] = $obj;
                        array_pop($collection);
                        break 1;
                    }
                }
            }
        }
        $collection = array_merge($collection, $later);

        foreach ($collection as $obj) {
            if ($obj instanceof Taxonomy) {
                add_action( 'init', array( $obj, 'register' ) );
            } elseif ($obj instanceof PostType) {
                /** @var PostType $obj */
                add_action( 'init', array( $obj, 'register' ) );

                if (is_string( $obj->getTitlePlaceholder() )) {
                    add_filter( 'enter_title_here', function($title) use ($obj) {
                        global $post;

                        if ($post->post_type == $obj->getId()) :
                            return $obj->getTitlePlaceholder();
                        else :
                            return $title;
                        endif;
                    } );
                }

                // edit_form_top
                if ($obj->getForm( 'top' )) {
                    add_action( 'edit_form_top', function($post) use ($obj) {
                        $obj->outputFormContent( $post, 'top' );
                    } );
                }

                // edit_form_after_title
                if ($obj->getForm( 'title' )) {
                    add_action( 'edit_form_after_title', function($post) use ($obj) {
                        $obj->outputFormContent( $post, 'title' );
                    } );
                }

                // edit_form_after_editor
                if ($obj->getForm( 'editor' )) {
                    add_action( 'edit_form_after_editor', function($post) use ($obj) {
                        $obj->outputFormContent( $post, 'editor' );
                    } );
                }

                // dbx_post_sidebar
                if ($obj->getForm( 'bottom' )) {
                    add_action( 'dbx_post_sidebar', function($post) use ($obj) {
                        $obj->outputFormContent( $post, 'bottom' );
                    } );
                }

            } elseif ($obj instanceof MetaBox) {
                add_action( 'admin_init', array( $obj, 'register' ) );
                add_action( 'add_meta_boxes', array( $obj, 'register' ) );
            }
        }
    }
}
