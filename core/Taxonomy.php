<?php
namespace TypeRocket;

/**
 * Taxonomy
 *
 * API for http://codex.wordpress.org/Function_Reference/register_taxonomy
 */
class Taxonomy extends Registrable
{

    private $id = null;
    public $singular = null;
    public $plural = null;
    public $use = null;
    public $form = null;
    public $post_types = array();
    public $args = array();

    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    public function getId() {
        return $this->id;
    }

    /**
     * Make Taxonomy. Do not use before init.
     *
     * @param string $singular singular name is required
     * @param string $plural plural name is required
     * @param array $settings args override and extend
     *
     * @return $this
     */
    function make( $singular, $plural, $settings = array() )
    {
        $this->form = array( array(
            'bottom' => null
        ) );

        $upperPlural   = ucwords( $plural );
        $upperSingular = ucwords( $singular );
        $lowerPlural   = strtolower( $plural );

        $this->plural   = $plural;
        $this->singular = $singular;
        $this->plural = Sanitize::underscore( $this->plural );
        $this->singular = Sanitize::underscore( $this->singular );
        $this->id = ! $this->id ? $this->singular : $this->id;

        $labels = array(
            'add_new_item'               => __( 'Add New ' . $upperSingular ),
            'add_or_remove_items'        => __( 'Add or remove ' . $lowerPlural ),
            'all_items'                  => __( 'All ' . $upperPlural ),
            'choose_from_most_used'      => __( 'Choose from the most used ' . $lowerPlural ),
            'edit_item'                  => __( 'Edit ' . $upperSingular ),
            'name'                       => __( $upperPlural ),
            'menu_name'                  => __( $upperPlural ),
            'new_item_name'              => __( 'New ' . $upperSingular . ' Name' ),
            'not_found'                  => __( 'No ' . $lowerPlural . ' found.' ),
            'parent_item'                => __( 'Parent ' . $upperSingular ),
            'parent_item_colon'          => __( 'Parent ' . $upperSingular . ':' ),
            'popular_items'              => __( 'Popular ' . $upperPlural ),
            'search_items'               => __( 'Search ' . $upperPlural ),
            'separate_items_with_commas' => __( 'Separate ' . $lowerPlural . ' with commas' ),
            'singular_name'              => __( $upperSingular ),
            'update_item'                => __( 'Update ' . $upperSingular ),
            'view_item'                  => __( 'View ' . $upperSingular )
        );

        if (array_key_exists( 'hierarchical', $settings ) && $settings['hierarchical'] === true) :
            $settings['hierarchical'] = true;
        else :
            $settings['hierarchical'] = false;
        endif;

        if (array_key_exists( 'capabilities', $settings ) && $settings['capabilities'] === true) :
            $settings['capabilities'] = array(
                'manage_terms' => 'manage_' . $this->plural,
                'edit_terms'   => 'manage_' . $this->plural,
                'delete_terms' => 'manage_' . $this->plural,
                'assign_terms' => 'edit_posts',
            );
        endif;

        $defaults = array(
            'labels'            => $labels,
            'show_admin_column' => false,
            'rewrite'           => array( 'slug' => $this->singular ),
        );

        $this->args = array_merge( $defaults, $settings );

        if (isset( $use )) :
            $this->uses( $use );
            $this->use = $use;
        endif;

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

        do_action( 'tr_register_taxonomy_' . $this->id, $this );
        register_taxonomy( $this->id, $this->post_types, $this->args );

        return $this;
    }

    private function addFormContent( $tag, $taxonomy, $args )
    {

        $func = 'add_form_content_' . $this->id . '_' . $args;

        echo '<div class="typerocket-container">';
        if (function_exists( $func )) :
            $func( $tag, $taxonomy );
        elseif (TR_DEBUG == true) :
            echo "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> Add content here by defining: <code>function {$func}() {}</code></div>";
        endif;
        echo '</div>';

    }

    function editFormBottom( $tag = null, $taxonomy = null )
    {
        $args = 'bottom';
        $this->addFormContent( $tag, $taxonomy, $args );
    }

    function addFormBottom( $taxonomy = null )
    {
        $args = 'bottom';
        $tag  = null;
        $this->addFormContent( $tag, $taxonomy, $args );
    }

    function addPostType( $v )
    {
        if (is_string( $v->id )) {
            $this->post_types = array_merge( $this->post_types, array( $v->id ) );
        }
    }

    function trUses( $v )
    {
        if (is_string( $v )) {
            $this->post_types = array_merge( $this->post_types, array( $v ) );
        }
    }

}