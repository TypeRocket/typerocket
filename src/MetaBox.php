<?php
namespace TypeRocket;

class MetaBox extends Registrable
{

    private $label = null;
    private $callback = null;
    private $context = null;
    private $priority = null;
    private $screens = array();

    /**
     * Make Meta Box
     *
     * @param string $name
     * @param null|string|array $screen
     * @param array $settings
     */
    public function __construct( $name, $screen = null, array $settings = array() )
    {
        $this->label = $this->id = $name;
        $this->id    = Sanitize::underscore( $this->id );

        if ( ! empty( $screen )) {
            $screen        = (array) $screen;
            $this->screens = array_merge( $this->screens, $screen );
        }

        if ( ! empty( $settings['callback'] )) {
            $this->callback = $settings['callback'];
        }
        if ( ! empty( $settings['label'] )) {
            $this->label = $settings['label'];
        }

        unset( $settings['label'] );

        $defaults = array(
            'context'  => 'normal', // 'normal', 'advanced', or 'side'
            'priority' => 'high', // 'high', 'core', 'default' or 'low'
            'args'     => array()
        ); // arguments to pass into your callback function.

        $settings = array_merge( $defaults, $settings );

        $this->context  = $settings['context'];
        $this->priority = $settings['priority'];
        $this->args     = $settings['args'];
    }

    /**
     * Set the meta box label
     *
     * @param $label
     *
     * @return $this
     */
    public function setLabel( $label )
    {

        $this->label = (string) $label;

        return $this;
    }

    /**
     * Set the meta box label
     *
     * @return $this->label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Add meta box to a screen
     *
     * @param string|array $screen
     *
     * @return $this
     */
    public function addScreen( $screen )
    {
        $this->screens = array_merge( $this->screens, (array) $screen );

        return $this;
    }

    /**
     * Add meta box to post type
     *
     * @param string|array|PostType $s
     *
     * @return $this
     */
    public function addPostType( $s )
    {
        if ($s instanceof PostType) {
            $s = $s->getId();
        } elseif (is_array( $s )) {
            foreach ($s as $n) {
                $this->addPostType( $n );
            }
        }

        if ( ! in_array( $s, $this->screens )) {
            $this->screens[] = $s;
        }

        return $this;

    }

    /**
     * Register meta box with WordPress
     *
     * @return $this
     */
    public function register()
    {
        global $post, $comment;

        $postType = null;

        if(!empty($post)) {
            $postType = get_post_type( $post->ID );
        }

        if (!empty($post) && post_type_supports( $postType, $this->id )) {
            $this->addPostType( $postType );
        }

        foreach ($this->screens as $screen) {
            if (( $postType == $screen && isset( $post ) ) ||
                ( $screen == 'comment' && isset( $comment ) ) ||
                ( $screen == 'dashboard' && ! isset( $post ) )
            ) {
                $obj = $this;

                $callback = function () use ( $obj ) {
                    $func     = 'add_meta_content_' . $obj->getId();
                    $callback = $obj->getCallback();

                    echo '<div class="typerocket-container">';
                    if (is_callable( $callback )) :
                        call_user_func_array( $callback, array( $obj ) );
                    elseif (function_exists( $func )) :
                        $func( $obj );
                    elseif (TR_DEBUG == true) :
                        echo "<div class=\"tr-dev-alert-helper\"><i class=\"icon tr-icon-bug\"></i> Add content here by defining: <code>function {$func}() {}</code></div>";
                    endif;
                    echo '</div>';
                };

                add_meta_box(
                    $this->id,
                    $this->label,
                    $callback,
                    $screen,
                    $this->context,
                    $this->priority
                );
            }
        }

        return $this;
    }

    /**
     * @return null
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param null $priority
     *
     * @return MetaBox
     */
    public function setPriority( $priority )
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @param null $context
     *
     * @return MetaBox
     */
    public function setContext( $context )
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return null
     */
    public function getContext()
    {
        return $this->context;
    }

    public function setCallback( $callback )
    {

        if (is_callable( $callback )) {
            $this->callback = $callback;
        } else {
            $this->callback = null;
        }

        return $this;
    }

    /**
     * @return null
     */
    public function getCallback()
    {
        return $this->callback;
    }


}