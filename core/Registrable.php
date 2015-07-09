<?php

namespace TypeRocket;

abstract class Registrable {
    protected $reserved_names = array(
        'attachment' => true,
        'attachment_id' => true,
        'author' => true,
        'author_name' => true,
        'action' => true,
        'calendar' => true,
        'cat' => true,
        'category' => true,
        'category__and' => true,
        'category__in' => true,
        'category__not_in' => true,
        'category_name' => true,
        'comments_per_page' => true,
        'comments_popup' => true,
        'customize_messenger_channel' => true,
        'customized' => true,
        'cpage' => true,
        'day' => true,
        'debug' => true,
        'error' => true,
        'exact' => true,
        'feed' => true,
        'hour' => true,
        'link_category' => true,
        'm' => true,
        'minute' => true,
        'monthnum' => true,
        'more' => true,
        'name' => true,
        'nav_menu' => true,
        'nonce' => true,
        'nopaging' => true,
        'offset' => true,
        'order' => true,
        'orderby' => true,
        'p' => true,
        'page' => true,
        'page_id' => true,
        'paged' => true,
        'pagename' => true,
        'pb' => true,
        'perm' => true,
        'post' => true,
        'post__in' => true,
        'post__not_in' => true,
        'post_format' => true,
        'post_mime_type' => true,
        'post_status' => true,
        'post_tag' => true,
        'post_type' => true,
        'posts' => true,
        'posts_per_archive_page' => true,
        'posts_per_page' => true,
        'preview' => true,
        'robots' => true,
        's' => true,
        'search' => true,
        'second' => true,
        'sentence' => true,
        'showposts' => true,
        'static' => true,
        'subpost' => true,
        'subpost_id' => true,
        'tag' => true,
        'tag__and' => true,
        'tag__in' => true,
        'tag__not_in' => true,
        'tag_id' => true,
        'tag_slug__and' => true,
        'tag_slug__in' => true,
        'taxonomy' => true,
        'tb' => true,
        'term' => true,
        'theme' => true,
        'type' => true,
        'w' => true,
        'withcomments' => true,
        'withoutcomments' => true,
        'year' => true );

    public $registrable = array(
        'TypeRocket\Taxonomy' => 'tr_taxonomy',
        'TypeRocket\PostType' => 'tr_post_type',
        'TypeRocket\Metabox' => 'tr_meta_box',
    );

    function __construct() {
        $this->init();
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            return null;
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this;
    }

    function reg() {
        Registry::add($this);

        return $this;
    }

    public function bake() {
        return $this;
    }

    private function init() {
    }

    protected function uses($use) {

        if(isset($use) && !is_array($use)) {
            $use = array($use);
        }

        $current_class = get_class($this);
        foreach($use as $v) :
            if(is_object($v)) {
                $class = get_class($v);
                $method = $this->registrable[$class];
                if( method_exists($this, $method) ) {
                    $this->$method($v);
                }
                else {
                    die('TypeRocket: You are passing the unsupported object '.$class.' into '. $current_class . '.');
                }
            } else {
                if( method_exists($this, 'tr_uses') ) {
                    $this->tr_uses($v);
                }
            }
        endforeach;
    }
}