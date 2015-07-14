<?php
namespace TypeRocket;

/**
 * Class Metabox
 * @package TypeRocket
 */
class Metabox extends Registrable
{

    private $id = null;
    public $label = null;
    public $use = array();
    public $post_types = array();
    public $args = array();

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

    function getId()
    {
        $this->getId();
    }

    /**
     * Make Meta Box
     *
     * @param null $name
     * @param array $settings
     *
     * @return $this
     */
    function make( $name, $settings = array() )
    {

        $this->label = $this->id = $name;
        $this->id    = Sanitize::underscore( $this->id );
        if (empty( $settings['callback'] )) {
            $settings['callback'] = array( $this, 'meta_content' );
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

    function apply( $use )
    {

        if (isset( $use )) :
            $this->uses( $use );
            $this->use = $use;
        endif;

        return $this;
    }

    function metaContent( $object, $box )
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
     * @param string|PostType $s
     */
    function addPostType( $s )
    {
        if (! is_string( $s )) {
            $s = (string) $s->getId();
        }
        $this->post_types = array_merge( $this->post_types, array( $s ) );
        $this->post_types = array_unique( $this->post_types );

    }

    function trUses( $v )
    {
        $this->addPostType( $v );
    }

    function bake()
    {

        global $post, $comment;
        $type = get_post_type( $post->ID );
        if (post_type_supports( $type, $this->id )) {
            $this->addPostType( $type );
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