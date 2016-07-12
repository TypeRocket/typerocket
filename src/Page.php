<?php

namespace TypeRocket;

class Page extends Registrable
{

    public $title = 'Admin Page Title';
    public $section = 'admin';
    public $icon = null;
    public $pages = [];
    /** @var null|Page parent page */
    public $parent = null;
    public $showTitle = true;

    /**
     * Page constructor.
     *
     * @param string $section set the
     * @param string $name
     * @param array $settings
     */
    public function __construct($section, $name, array $settings = [])
    {
        $this->title = $name;
        $this->section    = Sanitize::underscore( $section );
        $this->id    = Sanitize::underscore( $this->title );
        $this->args = array_merge( [
            'menu_title' => $this->title,
            'capability' => 'administrator',
            'position' => 99,
            'view' => Config::getPaths()['views'] . '/' . $this->section . '/' . $this->id . '.php',
            'slug' => $this->section . '_' . $this->id,
        ], $settings );

    }

    /**
     * Set the post type menu icon
     *
     * Add the CSS needed to create the icon for the menu
     *
     * @param $name
     *
     * @return $this
     */
    public function setIcon( $name )
    {
        $name       = strtolower( $name );
        $icons      = Config::getIcons();

        if( ! $icons instanceof Icons ) {
            $icons = new Icons();
        }

        $this->icon = $icons[$name];

        add_action( 'admin_head', \Closure::bind( function() use ($icons) {
            $slug = $this->args['slug'];
            $icon = $this->getIcon();
            echo "
            <style type=\"text/css\">
                #adminmenu #toplevel_page_{$slug} .wp-menu-image:before {
                    font: {$icons->fontWeight} {$icons->fontSize} {$icons->fontFamily} !important;
                    content: '{$icon}';
                    speak: none;
                    -webkit-font-smoothing: antialiased;
                }
            </style>";
        }, $this ) );

        return $this;
    }

    /**
     * Get the post type icon
     *
     * @return null
     */
    public function getIcon() {
        return $this->icon;
    }

    /**
     * Get the slug
     *
     * @return mixed
     */
    public function getSlug() {
        return $this->args['slug'];
    }

    /**
     * Set the slug
     *
     * @param $slug
     *
     * @return $this
     */
    public function setSlug( $slug ) {
        $this->args['slug'] = $slug;

        return $this;
    }

    /**
     * Set the parent page
     *
     * @param \TypeRocket\Page $parent
     *
     * @return $this
     */
    public function setParent( Page $parent ) {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get the parent page
     *
     * @return null|\TypeRocket\Page
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Remove title from page
     *
     * @return $this
     */
    public function removeTitle()
    {
        $this->showTitle = false;

        return $this;
    }

    /**
     * Register with WordPress
     *
     * Override this in concrete classes
     *
     * @return $this
     */
    public function register()
    {
        $menu_title = $this->args['menu_title'];
        $capability = $this->args['capability'];
        $slug = $this->getSlug();
        $position = $this->args['position'];

        $callback = function() {

            $view = $this->args['view'];

            do_action('tr_page_start_view_' . $this->id, $this);
            echo '<div id="typerocket-admin-page" class="wrap typerocket-container">';

            if( $this->showTitle ) {
                echo '<h2>'. $this->title .'</h2>';
            }

            echo '<div>';
            if (file_exists( $view )) {
                include( $view );
            } elseif( TR_DEBUG == true ) {
                echo "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> Add content here by creating: <code>{$view}</code></div>";
            }
            echo '</div></div>';
            do_action('tr_page_end_view_' . $this->id, $this);

        };

        if( ! $this->parent ) {
            add_menu_page( $this->title, $menu_title, $capability, $slug, \Closure::bind( $callback, $this ), '', $position);
            if( !empty($this->pages) ) {
                add_submenu_page( $slug , $this->title, $menu_title, $capability, $slug );
            }
        } else {
            add_submenu_page( $this->parent->getSlug() , $this->title, $menu_title, $capability, $slug, \Closure::bind( $callback, $this ) );
        }

        return $this;
    }

    /**
     * Add page to page
     *
     * @param string|Page $s
     *
     * @return $this
     */
    public function addPage( $s )
    {

        if ( $s instanceof Page && ! in_array( $s, $this->pages )) {
            $this->pages[] = $s;
            $s->setParent( $this );
        } elseif( is_array($s) ) {
            foreach($s as $n) {
                $this->addPage($n);
            }
        }

        return $this;

    }
}