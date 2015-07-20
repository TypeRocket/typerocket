<?php
namespace TypeRocket;

class Metabox extends Registrable
{

    private $label = null;
    private $post_types = array();

    /**
     * Make Meta Box
     *
     * @param null $name
     * @param array $settings
     *
     * @return $this
     */
    public function setup( $name, $settings = array() )
    {

        $this->label = $this->id = $name;
        $this->id    = Sanitize::underscore( $this->id );
        if (empty( $settings['callback'] )) {
            $settings['callback'] = array( $this, 'metaContent' );
        }
        if (empty( $settings['label'] )) {
            $settings['label'] = $this->label;
        } else {
            $this->label = $settings['label'];
        }

        unset( $settings['label'] );

        $defaults = array(
            'context'  => 'normal', // 'normal', 'advanced', or 'side'
            'priority' => 'high', // 'high', 'core', 'default' or 'low'
            'args'     => array()
        ); // arguments to pass into your callback function.

        $settings = array_merge( $defaults, $settings );

        $this->args = $settings;

        return $this;
    }

    /**
     * Add content inside form hook and wrap with the TypeRocket container
     *
     * @param $object
     * @param $box
     */
    public function metaContent( $object, $box )
    {
        $func = 'add_meta_content_' . $this->id;

        echo '<div class="typerocket-container">';
        if (function_exists( $func )) :
            $func( $object, $box );
        elseif (TR_DEBUG == true) :
            echo "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> Add content here by defining: <code>function {$func}() {}</code></div>";
        endif;
        echo '</div>';
    }

    /**
     * Add metabox to post type
     *
     * @param string|PostType $s
     */
    public function postTypeRegistrationById( $s )
    {
        if ( ! is_string( $s )) {
            $s = (string) $s->getId();
        }

        if ( ! in_array( $s, $this->post_types )) {
            $this->post_types[] = $s;
        }

    }

    /**
     * Apply taxonomy to a post type by string
     *
     * @param $postTypeId
     */
    public function stringRegistration( $postTypeId )
    {
        $this->postTypeRegistrationById( $postTypeId );
    }

    /**
     * Register metabox with WordPress
     *
     * @return $this
     */
    public function register()
    {

        global $post, $comment;
        $type = get_post_type( $post->ID );
        if (post_type_supports( $type, $this->id )) {
            $this->postTypeRegistrationById( $type );
        }

        foreach ($this->post_types as $v) {
            if ($type == $v || ( $v == 'comment' && isset( $comment ) )) {
                add_meta_box(
                    $this->id,
                    $this->label,
                    $this->args['callback'],
                    $v,
                    $this->args['context'],
                    $this->args['priority'],
                    $this->args['args']
                );
            }
        }

        return $this;
    }

}
