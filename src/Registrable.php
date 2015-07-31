<?php
namespace TypeRocket;

abstract class Registrable
{

    protected $use = array();
    protected $id = null;
    protected $args = array();
    protected $reservedNames = array(
        'attachment'                  => true,
        'attachment_id'               => true,
        'author'                      => true,
        'author_name'                 => true,
        'action'                      => true,
        'calendar'                    => true,
        'cat'                         => true,
        'category'                    => true,
        'category__and'               => true,
        'category__in'                => true,
        'category__not_in'            => true,
        'category_name'               => true,
        'comments_per_page'           => true,
        'comments_popup'              => true,
        'customize_messenger_channel' => true,
        'customized'                  => true,
        'cpage'                       => true,
        'day'                         => true,
        'debug'                       => true,
        'error'                       => true,
        'exact'                       => true,
        'feed'                        => true,
        'hour'                        => true,
        'link_category'               => true,
        'm'                           => true,
        'minute'                      => true,
        'monthnum'                    => true,
        'more'                        => true,
        'name'                        => true,
        'nav_menu'                    => true,
        'nonce'                       => true,
        'nopaging'                    => true,
        'offset'                      => true,
        'order'                       => true,
        'orderby'                     => true,
        'p'                           => true,
        'page'                        => true,
        'page_id'                     => true,
        'paged'                       => true,
        'pagename'                    => true,
        'pb'                          => true,
        'perm'                        => true,
        'post'                        => true,
        'post__in'                    => true,
        'post__not_in'                => true,
        'post_format'                 => true,
        'post_mime_type'              => true,
        'post_status'                 => true,
        'post_tag'                    => true,
        'post_type'                   => true,
        'posts'                       => true,
        'posts_per_archive_page'      => true,
        'posts_per_page'              => true,
        'preview'                     => true,
        'robots'                      => true,
        's'                           => true,
        'search'                      => true,
        'second'                      => true,
        'sentence'                    => true,
        'showposts'                   => true,
        'static'                      => true,
        'subpost'                     => true,
        'subpost_id'                  => true,
        'tag'                         => true,
        'tag__and'                    => true,
        'tag__in'                     => true,
        'tag__not_in'                 => true,
        'tag_id'                      => true,
        'tag_slug__and'               => true,
        'tag_slug__in'                => true,
        'taxonomy'                    => true,
        'tb'                          => true,
        'term'                        => true,
        'theme'                       => true,
        'type'                        => true,
        'w'                           => true,
        'withcomments'                => true,
        'withoutcomments'             => true,
        'year'                        => true
    );

    public function __get( $property )
    {
    }

    public function __set( $property, $value )
    {
    }

    /**
     * Set the Registrable ID for WordPress to use. Don't use reserved names.
     *
     * @param $id
     *
     * @return $this
     */
    public function setId( $id )
    {

        if(in_array($id, $this->reservedNames)) {
            $this->id = Sanitize::underscore( $id );
        } else {
            die('ID Reserved: ' . $id);
        }

        return $this;
    }

    /**
     * Get the ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Arguments
     *
     * @param array $args
     *
     * @return $this
     */
    public function setArguments( array $args )
    {
        $this->args = $args;

        return $this;
    }

    /**
     * Get Arguments
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->args;
    }

    /**
     * Get Argument by key
     *
     * @return string
     */
    public function getArgument( $key )
    {
        if( ! array_key_exists($key, $this->args)) {
            return null;
        }

        return $this->args[$key];
    }

    /**
     * Set Argument by key
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setArgument( $key, $value )
    {

        $this->args[$key] = $value;

        return $this;
    }

    /**
     * Remove Argument by key
     *
     * @param $key
     *
     * @return $this
     */
    public function removeArgument( $key )
    {
        if( array_key_exists($key, $this->args)) {
            unset($this->args[$key]);
        }

        return $this;
    }

    protected function dieIfReserved()
    {
        if (array_key_exists( $this->id, $this->reservedNames )) {
            die( 'TypeRocket: Error, you are using the reserved wp name "' . $this->id . '".' );
        }
    }

    /**
     * Use other Registrable objects or string IDs
     *
     * @param string|MetaBox|PostType|Taxonomy $args variadic
     *
     * @return $this
     */
    public function apply( $args )
    {

        if( ! is_array($args)) {
            $args = func_get_args();
        }

        if ( ! empty($args) && is_array( $args ) ) {
            $this->use = array_merge( $this->use, $args );
        }

        $this->uses();

        return $this;
    }

    /**
     * Add Registrable to the registry
     *
     * @return $this
     */
    public function addToRegistry()
    {
        Registry::addRegistrable( $this );

        return $this;
    }

    /**
     * Register with WordPress
     *
     * Override this in concrete classes
     *
     * @return $this
     */
    abstract public function register();

    /**
     * Used with the apply method to connect Registrable objects together.
     */
    protected function uses()
    {

        foreach ($this->use as $obj) {
            if ($obj instanceof Registrable) {
                $class  = get_class( $obj );
                $class = substr($class,11);
                $method = 'add' . $class;
                if (method_exists( $this, $method )) {
                    $this->$method( $obj );
                } else {
                    $current_class = get_class( $this );
                    die( 'TypeRocket: You are passing the unsupported object ' . $class . ' into ' . $current_class . '.' );
                }
            }
        }
    }

    /**
     * Get the Use
     *
     * @return array
     */
    public function getApplied()
    {
        return $this->use;
    }
}
