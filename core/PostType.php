<?php
namespace TypeRocket;

class PostType extends Registrable
{

    public $id = null;
    public $singular = null;
    public $plural = null;
    private $title = null;
    private $form = null;
    public $use = null;
    public $taxonomies = array();
    public $args = array();
    private $icon = null;

    function setIcon( $name )
    {
        $name       = strtolower( $name );
        $icons = new Icons();
        $this->icon = $icons[$name];
        add_action( 'admin_head', array( $this, 'style' ) );

        return $this;
    }

    public function getTitlePlaceholder()
    {
        return $this->title;
    }

    public function setTitlePlaceholder($text)
    {
        return $this->title = (string) $text;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    function setId( $id )
    {
        $this->id = $id;

        return $this;
    }

    function getId() {
        return $this->id;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function getFrom( $key )
    {
        return $this->form[$key];
    }

    /**
     * @param bool|true|callable $value
     *
     * @return $this
     */
    public function setTitleFrom($value = true) {

        if( is_callable($value) ) {
            $this->form['title'] = $value;
        } else {
            $this->form['title'] = true;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeTitleFrom() {
        $this->form['title'] = null;

        return $this;
    }

    /**
     * @param bool|true|callable $value
     *
     * @return $this
     */
    public function setTopFrom($value = true) {
        if( is_callable($value) ) {
            $this->form['top'] = $value;
        } else {
            $this->form['top'] = true;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeTopFrom() {
        $this->form['top'] = null;

        return $this;
    }

    /**
     * @param bool|true|callable $value
     *
     * @return $this
     */
    public function setBottomFrom($value = true) {
        if( is_callable($value) ) {
            $this->form['bottom'] = $value;
        } else {
            $this->form['bottom'] = true;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeBottomFrom() {
        $this->form['bottom'] = null;

        return $this;
    }

    /**
     * @param bool|true|callable $value
     *
     * @return $this
     */
    public function setEditorFrom($value = true) {
        if( is_callable($value) ) {
            $this->form['editor'] = $value;
        } else {
            $this->form['editor'] = true;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeEditorFrom() {
        $this->form['editor'] = null;

        return $this;
    }

    public function style()
    { ?>

        <style type="text/css">
            #adminmenu #menu-posts-<?php echo $this->id; ?> .wp-menu-image:before {
                font: 400 15px/1 'typerocket-icons' !important;
                content: '<?php echo $this->icon; ?>';
                speak: none;
                -webkit-font-smoothing: antialiased;
            }
        </style>

    <?php }

    /**
     * Make Post Type. Do not use before init.
     *
     * @param string $singular singular name is required
     * @param string $plural plural name is required
     * @param array $settings args override and extend
     *
     * @return $this
     */
    function make( $singular, $plural, $settings = array() )
    {

        $this->form = array(
            array(
                'top'    => null,
                'title'  => null,
                'editor' => null,
                'bottom' => null
            )
        );

        // setup object for later use
        $this->plural   = $plural;
        $this->singular = $singular;
        $this->plural   = Sanitize::underscore( $this->plural );
        $this->singular = Sanitize::underscore( $this->singular );
        $this->id       = ! $this->id ? $this->singular : $this->id;

        // make lowercase
        $singular      = strtolower( $singular );
        $plural        = strtolower( $plural );
        $upperSingular = ucwords( $singular );
        $upperPlural   = ucwords( $plural );

        $labels = array(
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New ' . $upperSingular,
            'edit_item'          => 'Edit ' . $upperSingular,
            'menu_name'          => $upperPlural,
            'name'               => $upperPlural,
            'new_item'           => 'New ' . $upperSingular,
            'not_found'          => 'No ' . $plural . ' found',
            'not_found_in_trash' => 'No ' . $plural . ' found in Trash',
            'parent_item_colon'  => '',
            'search_items'       => 'Search ' . $upperPlural,
            'singular_name'      => $upperSingular,
            'view_item'          => 'View ' . $upperSingular,
        );

        if (array_key_exists( 'capabilities', $settings ) && $settings['capabilities'] === true) :
            $settings['capabilities'] = array(
                'publish_posts'       => 'publish_' . $this->plural,
                'edit_post'           => 'edit_' . $this->singular,
                'edit_posts'          => 'edit_' . $this->plural,
                'edit_others_posts'   => 'edit_others_' . $this->plural,
                'delete_post'         => 'delete_' . $this->singular,
                'delete_posts'        => 'delete_' . $this->plural,
                'delete_others_posts' => 'delete_others_' . $this->plural,
                'read_post'           => 'read_' . $this->singular,
                'read_private_posts'  => 'read_private_' . $this->plural,
            );
        endif;

        $defaults = array(
            'labels'      => $labels,
            'description' => $plural,
            'rewrite'     => array( 'slug' => $this->plural ),
            'public'      => true,
            'supports'    => array( 'title', 'editor' ),
            'has_archive' => true,
            'taxonomies'  => array()
        );

        if (array_key_exists( 'admin_only', $settings ) && $settings['admin_only'] == true) {
            $admin_only = array(
                'public'      => false,
                'has_archive' => false,
                'show_ui'     => true
            );
            unset( $settings['admin_only'] );
            $defaults = array_merge( $defaults, $admin_only );
        }


        if (array_key_exists( 'taxonomies', $settings )) {
            $this->taxonomies       = array_merge( $this->taxonomies, $settings['taxonomies'] );
            $settings['taxonomies'] = $this->taxonomies;
        }

        $this->args = array_merge( $defaults, $settings );

        return $this;
    }

    function apply( $use )
    {

        if (isset( $use )) :
            $this->uses( $use );
            $this->use = $use;
        endif;

        return $this;
    }

    function bake()
    {
        if (array_key_exists( $this->id, $this->reservedNames )) :
            die( 'TypeRocket: Error, you are using the reserved wp name "' . $this->id . '".' );
        endif;

        $id = Sanitize::underscore( $this->id );

        do_action( 'tr_register_post_type_' . $id, $this );
        register_post_type( $this->id, $this->args );

        return $this;
    }

    /**
     * @param string|Metabox $s
     */
    function addMetaBox( $s )
    {
        if (! is_string( $s )) {
            $s = (string) $s->getId();
        }
        $this->args['supports'] = array_merge( $this->args['supports'], array( $s ) );
        $this->args['supports'] = array_unique( $this->args['supports'] );

    }

    /**
     * @param string|Taxonomy $s
     */
    function addTaxonomy( $s )
    {

        if (! is_string( $s )) {
            $s = (string) $s->getId();
        }

        $this->taxonomies         = array_merge( $this->taxonomies, array( $s ) );
        $this->taxonomies         = array_unique( $this->taxonomies );
        $this->args['taxonomies'] = $this->taxonomies;
    }

    function addFormContent( $post, $args )
    {
        if ($post->post_type == $this->id) :

            $id = Sanitize::underscore( $this->id );

            $func = 'add_form_content_' . $id . '_' . $args;

            echo '<div class="typerocket-container">';
            if (function_exists( $func )) :
                $func( $post );
            elseif (TR_DEBUG == true) :
                echo "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> Add content here by defining: <code>function {$func}() {}</code></div>";
            endif;
            echo '</div>';


        endif;
    }

    function editFormTop( $post )
    {
        $args = 'top';
        $this->addFormContent( $post, $args );
    }

    function edit_form_after_title( $post )
    {
        $args = 'title';
        $this->addFormContent( $post, $args );
    }

    function editFormAfterEditor( $post )
    {
        $args = 'editor';
        $this->addFormContent( $post, $args );
    }

    function dbxPostSidebar( $post )
    {
        $args = 'bottom';
        $this->addFormContent( $post, $args );
    }

    function enterTitleHere( $s )
    {
        global $post;

        if ($post->post_type == $this->id) :
            return $this->title;
        else :
            return $s;
        endif;
    }

    function trUses( $v )
    {
        $this->addTaxonomy( $v );
    }

}
