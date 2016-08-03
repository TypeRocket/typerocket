<?php
namespace TypeRocket;

abstract class Registrable
{

    protected $use = [];
    protected $id = null;
    protected $args = [];
    protected $reservedNames = [
        'attachment',
        'attachment_id',
        'author',
        'author_name',
        'action',
        'calendar',
        'cat',
        'category',
        'category__and',
        'category__in',
        'category__not_in',
        'category_name',
        'comments_per_page',
        'comments_popup',
        'customize_messenger_channel',
        'customized',
        'cpage',
        'day',
        'debug',
        'error',
        'exact',
        'feed',
        'hour',
        'link_category',
        'm',
        'minute',
        'monthnum',
        'more',
        'name',
        'nav_menu',
        'nonce',
        'nopaging',
        'offset',
        'order',
        'orderby',
        'p',
        'page',
        'page_id',
        'paged',
        'pagename',
        'pb',
        'perm',
        'post',
        'post__in',
        'post__not_in',
        'post_format',
        'post_mime_type',
        'post_status',
        'post_tag',
        'post_type',
        'posts',
        'posts_per_archive_page',
        'posts_per_page',
        'preview',
        'robots',
        's',
        'search',
        'second',
        'sentence',
        'showposts',
        'static',
        'subpost',
        'subpost_id',
        'tag',
        'tag__and',
        'tag__in',
        'tag__not_in',
        'tag_id',
        'tag_slug__and',
        'tag_slug__in',
        'taxonomy',
        'tb',
        'term',
        'theme',
        'type',
        'w',
        'withcomments',
        'withoutcomments',
        'year'
    ];

    /**
     * Set the Registrable ID for WordPress to use. Don't use reserved names.
     *
     * @param $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = Sanitize::underscore($id);
        $this->dieIfReserved();

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
    public function setArguments(array $args)
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
    public function getArgument($key)
    {
        if ( ! array_key_exists($key, $this->args)) {
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
    public function setArgument($key, $value)
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
    public function removeArgument($key)
    {
        if (array_key_exists($key, $this->args)) {
            unset($this->args[$key]);
        }

        return $this;
    }

    protected function dieIfReserved()
    {
        if (in_array($this->id, $this->reservedNames)) {
            die('TypeRocket: Error, you are using the reserved wp name "' . $this->id . '".');
        }
    }

    /**
     * Use other Registrable objects or string IDs
     *
     * @param string|MetaBox|PostType|Taxonomy $args variadic
     *
     * @return $this
     */
    public function apply($args)
    {

        if ( ! is_array($args)) {
            $args = func_get_args();
        }

        if ( ! empty($args) && is_array($args)) {
            $this->use = array_merge($this->use, $args);
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
        Registry::addRegistrable($this);

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
                $class  = get_class($obj);
                $class  = substr($class, 11);
                $method = 'add' . $class;
                if (method_exists($this, $method)) {
                    $this->$method($obj);
                } else {
                    $current_class = get_class($this);
                    die('TypeRocket: You are passing the unsupported object ' . $class . ' into ' . $current_class . '.');
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
