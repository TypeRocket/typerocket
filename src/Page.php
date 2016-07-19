<?php

namespace TypeRocket;

class Page extends Registrable
{

    public $title = 'Admin Page Title';
    public $resource = 'admin';
    public $action = 'index';
    public $icon = null;
    public $pages = [];
    /** @var null|Page parent page */
    public $parent = null;
    public $showTitle = true;
    public $showMenu = true;
    public $showAddNewButton = false;
    public $builtin = [
        'tools' => 'tools.php',
        'dashboard' => 'index.php',
        'media' => 'upload.php',
        'appearance' => 'themes.php',
        'plugins' => 'plugins.php',
        'users' => 'users.php',
        'settings' => 'options-general.php'
    ];

    /**
     * Page constructor.
     *
     * @param string $resource set the
     * @param $action
     * @param string $name
     * @param array $settings
     */
    public function __construct($resource, $action, $name, array $settings = [])
    {
        $this->title    = $name;
        $this->resource = Sanitize::underscore( $resource );
        $this->id       = Sanitize::underscore( $this->title );
        $this->action   = Sanitize::underscore( $action );
        $this->args     = array_merge( [
            'menu' => $this->title,
            'capability' => 'administrator',
            'position' => 99,
            'view' => Config::getPaths()['views'] . '/' . $this->resource . '/' . $this->action . '.php',
            'slug' => $this->resource . '_' . $this->action,
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

        $this->icon = !empty($icons[$name]) ? $icons[$name] : null;
        if( ! $this->icon ) {
            return $this;
        }

        add_action( 'admin_head', \Closure::bind( function() use ($icons) {
            $slug = $this->args['slug'];
            $icon = $this->getIcon();
            echo "
            <style type=\"text/css\">
                #adminmenu #toplevel_page_{$slug} .wp-menu-image:before {
                    font: {$icons->fontWeight} {$icons->fontSize} {$icons->fontFamily} !important;
                    content: '{$icon}';
                    speak: none;
                    top: 2px;
                    position: relative;
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
     * Get Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set Title
     *
     * @param $title
     *
     * @return $this
     */
    public function setTitle( $title )
    {
        $this->title = $title;

        return $this;
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
     * Remove menu
     *
     * @return $this
     */
    public function removeMenu()
    {
        $this->showMenu = false;

        return $this;
    }

    /**
     * Show create button
     *
     * @return $this
     */
    public function addNewButton() {
        $this->showAddNewButton = true;

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
        $menu_title = $this->args['menu'];
        $capability = $this->args['capability'];
        $slug = $this->getSlug();
        $position = $this->args['position'];

        $callback = function() {

            $view = $this->args['view'];

            do_action('tr_page_start_view_' . $this->id, $this);
            echo '<div id="typerocket-admin-page" class="wrap typerocket-container">';

            if( $this->showAddNewButton ) {
                // page-title-action
                $url = '';

                if( $this->pages ) {
                    foreach ($this->pages as $page) {
                        if($page->action == 'create') {
                            $wp_page = !empty($this->builtin[$page->section]) ? $this->builtin[$page->section] : 'admin.php';
                            $url =  admin_url() . $wp_page . '?page=' . $page->getSlug();
                            break;
                        }
                    }
                }


                $action = ' <a href="'.$url.'" class="page-title-action">Add New</a>';
                echo '<h1>'. $this->title . $action . '</h1>';
            } elseif( $this->showTitle ) {
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

        if( array_key_exists( $this->resource, $this->builtin ) ) {
            add_submenu_page( $this->builtin[$this->resource] , $this->title, $menu_title, $capability, $slug, \Closure::bind( $callback, $this ) );
        } elseif( ! $this->parent ) {
            add_menu_page( $this->title, $menu_title, $capability, $slug, \Closure::bind( $callback, $this ), '', $position);
            if( $this->hasShownSubPages() ) {
                add_submenu_page( $slug , $this->title, $menu_title, $capability, $slug );
            }
        } else {
            $parent_slug = $this->parent->getSlug();
            add_submenu_page( $parent_slug, $this->title, $menu_title, $capability, $slug, \Closure::bind( $callback, $this ) );

            if( ! $this->showMenu ) {
                add_action( 'admin_head', function() use ($parent_slug, $slug) {
                    remove_submenu_page( $parent_slug, $slug );
                } );
            }
        }

        return $this;
    }

    /**
     * Has shown sub pages
     *
     * @return bool
     */
    public function hasShownSubPages()
    {
        if( ! empty( $this->pages ) ) {
            foreach($this->pages as $page) {
                if( $page->showMenu ) {
                    return true;
                }
            }
        }

        return false;
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