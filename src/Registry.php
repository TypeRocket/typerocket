<?php
namespace TypeRocket;

class Registry
{

    private static $collection = array();
    private static $postTypes = array( 'post' => 'posts', 'page' => 'pages' );

    /**
     * Add a post type resource
     *
     * @param string $id post type id
     * @param string $resource resource name ex. posts, pages, books
     */
    public static function addPostTypeResource($id, $resource) {
        self::$postTypes[$id] = $resource;
    }

    /**
     * Get the post type resource
     *
     * @param $id
     *
     * @return null
     */
    public static function getPostTypeResource($id) {
        return ! empty(self::$postTypes[$id]) ? self::$postTypes[$id] : null;
    }

    /**
     * Add Registrable objects to collection
     *
     * @param null|Registrable|string $obj
     */
    public static function addRegistrable( $obj = null )
    {
        if ( $obj instanceof Registrable) {
            self::$collection[] = $obj;
        }
    }

    /**
     * Loop through each Registrable and add hooks automatically
     */
    public static function initHooks()
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

                        if(!empty($post)) {
                            if ( $post->post_type == $obj->getId() ) {
                                return $obj->getTitlePlaceholder();
                            }
                        }

                        return $title;

                    } );
                }

                self::postTypeFormContent($obj);

            } elseif ($obj instanceof MetaBox) {
                add_action( 'admin_init', array( $obj, 'register' ) );
                add_action( 'add_meta_boxes', array( $obj, 'register' ) );
            }
        }
    }

    /**
     * Add post type form hooks
     *
     * @param PostType $obj
     */
    private static function postTypeFormContent( PostType $obj) {

        /**
         * @param \WP_Post$post
         * @param string $type
         * @param PostType $obj
         */
        $callback = function( $post, $type, $obj )
        {
            if ($post->post_type == $obj->getId()) {
                $func = 'add_form_content_' . $obj->getId() . '_' . $type;
                echo '<div class="typerocket-container">';

                $form = $obj->getForm( $type );
                if (is_callable( $form )) {
                    call_user_func( $form );
                } elseif (function_exists( $func )) {
                    call_user_func( $func, $post );
                } elseif (TR_DEBUG == true) {
                    echo "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> Add content here by defining: <code>function {$func}() {}</code></div>";
                }
                echo '</div>';
            }
        };

        // edit_form_top
        if ($obj->getForm( 'top' )) {
            add_action( 'edit_form_top', function($post) use ($obj, $callback) {
                $type = 'top';
                call_user_func_array($callback, array($post, $type, $obj));
            } );
        }

        // edit_form_after_title
        if ($obj->getForm( 'title' )) {
            add_action( 'edit_form_after_title', function($post) use ($obj, $callback) {
                $type = 'title';
                call_user_func_array($callback, array($post, $type, $obj));
            } );
        }

        // edit_form_after_editor
        if ($obj->getForm( 'editor' )) {
            add_action( 'edit_form_after_editor', function($post) use ($obj, $callback) {
                $type = 'editor';
                call_user_func_array($callback, array($post, $type, $obj));
            } );
        }

        // dbx_post_sidebar
        if ($obj->getForm( 'bottom' )) {
            add_action( 'dbx_post_sidebar', function($post) use ($obj, $callback) {
                $type = 'bottom';
                call_user_func_array($callback, array($post, $type, $obj));
            } );
        }

    }
}
