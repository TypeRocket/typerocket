<?php
namespace TypeRocket;

class SeoPlugin
{

    public $itemId = null;

    public function __construct()
    {
        if ( ! function_exists( 'add_action' ) ) {
            echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
            exit;
        }
    }

    public function setup()
    {
        if ( ! defined( 'WPSEO_URL' ) && ! defined( 'AIOSEOP_VERSION' ) ) {
            define( 'TR_SEO', '1.0' );
            add_action('tr_model', array($this, 'fillable'), 9999999999, 2 );
            add_action( 'wp_head', array( $this, 'head_data' ), 0 );
            add_action( 'template_redirect', array( $this, 'loaded' ), 0 );
            add_filter( 'wp_title', array( $this, 'title' ), 100, 3 );
            remove_action( 'wp_head', 'rel_canonical' );
            add_action( 'wp', array( $this, 'redirect' ), 99, 1 );


            if ( is_admin() ) {
                add_action( 'admin_init', array( $this, 'css' ) );
                add_action( 'add_meta_boxes', array( $this, 'seo_meta' ) );
            }
        }
    }

    public function fillable( $model )
    {
        global $post;

        if($model instanceof Models\PostTypesModel) {
            $fillable = $model->getFillableFields();
            /** @var \WP_Post $data */
            $types = get_post_types(array('public' => true));
            if(!empty($fillable) && !empty($types[$post->post_type]) ) {
                $model->appendFillableField('seo');
            }
        }
    }

    public function loaded()
    {
        $this->itemId = get_queried_object_id();
    }

    public function seo_meta()
    {
        $publicTypes = get_post_types( array( 'public' => true ) );
        $args        = array(
            'label'    => 'Search Engine Optimization',
            'priority' => 'low',
            'callback' => array( $this, 'meta' )
        );
        $obj         = new MetaBox( 'tr_seo', null, $args );
        $obj->addPostType( $publicTypes )->register();
    }

    // Page Title
    public function title( $title, $sep = '', $other = '' )
    {
        $newTitle = tr_posts_field( '[seo][meta][title]', $this->itemId );

        if ( $newTitle != null ) {
            return $newTitle;
        } else {
            return $title;
        }

    }

    public function title_tag()
    {
        echo '<title>' . $this->title( '|', false, 'right' ) . "</title>";
    }

    // head meta data
    public function head_data()
    {
        $object_id = (int) $this->itemId;

        // meta vars
        $url              = get_the_permalink($object_id);
        $seo              = tr_posts_field('[seo][meta]', $object_id);
        $desc             = esc_attr( $seo['description'] );
        $og_title         = esc_attr( $seo['og_title'] );
        $og_desc          = esc_attr( $seo['og_desc'] );
        $img              = esc_attr( $seo['meta_img'] );
        $canon            = esc_attr( $seo['canonical'] );
        $robots['index']  = esc_attr( $seo['index'] );
        $robots['follow'] = esc_attr( $seo['follow'] );
        $tw['card']       = esc_attr( $seo['tw_card'] );
        $tw['site']       = esc_attr( $seo['tw_site'] );
        $tw['image']      = esc_attr( $seo['tw_img'] );
        $tw['title']      = esc_attr( $seo['tw_title'] );
        $tw['desc']      = esc_attr( $seo['tw_desc'] );

        // Extra
        if ( ! empty( $canon ) ) {
            echo "<link rel=\"canonical\" href=\"{$canon}\" />";
        } else {
            rel_canonical();
        }

        // Robots
        if ( ! empty( $robots ) ) {
            $robot_data = '';
            foreach ( $robots as $value ) {
                if ( ! empty( $value ) && $value != 'none' ) {
                    $robot_data .= $value . ', ';
                }
            }

            $robot_data = substr( $robot_data, 0, - 2 );
            if ( ! empty( $robot_data ) ) {
                echo "<meta name=\"robots\" content=\"{$robot_data}\" />";
            }
        }

        // OG
        if ( ! empty( $og_title ) ) {
            echo "<meta property=\"og:title\" content=\"{$og_title}\" />";
        }
        if ( ! empty( $og_desc ) ) {
            echo "<meta property=\"og:description\" content=\"{$og_desc}\" />";
        }
        if ( ! empty( $img ) ) {
            $src = wp_get_attachment_image_src($img, 'full');
            $src = $src[0];
            $prefix = 'https';

            if (substr($src, 0, strlen($prefix)) == $prefix) {
                $src = substr($src, strlen($prefix));
                $src = 'http' . $src;
            }

            echo "<meta property=\"og:image\" content=\"{$src}\" />";
        }
        if( ! empty($url) ) {
            echo "<meta property=\"og:url\" content=\"{$url}\" />";
        }

        // Twitter
        if( ! empty($tw['card']) ) {
            echo "<meta name=\"twitter:card\" content=\"{$tw['card']}\" />";
        }
        if( ! empty($tw['site']) ) {
            echo "<meta name=\"twitter:site\" content=\"{$tw['site']}\" />";
        }
        if( ! empty($tw['image']) ) {
            $src = wp_get_attachment_image_src($tw['image'], 'full');
            $src = $src[0];

            echo "<meta name=\"twitter:image\" content=\"{$src}\" />";
        }
        if( ! empty($tw['title']) ) {
            echo "<meta name=\"twitter:title\" content=\"{$tw['title']}\" />";
        }
        if( ! empty($tw['desc']) ) {
            echo "<meta name=\"twitter:desciption\" content=\"{$tw['desc']}\" />";
        }

        // Basic
        if ( ! empty( $desc ) ) {
            echo "<meta name=\"description\" content=\"{$desc}\" />";
        }
    }

    // 301 Redirect
    public function redirect()
    {
        if ( is_singular() ) {
            $redirect = tr_posts_field( '[seo][meta][redirect]', $this->itemId );
            if ( ! empty( $redirect ) ) {
                wp_redirect( $redirect, 301 );
                exit;
            }
        }
    }

    // CSS
    public function css()
    {
        $paths = Config::getPaths();
        $path  = $paths['urls']['plugins'] . '/seo/';
        wp_enqueue_style( 'tr-seo', $path . 'style.css' );
        wp_enqueue_script( 'tr-seo', $path . 'script.js', array( 'jquery' ), '1.0', true );
    }

    public function meta()
    {
        echo '<div class="typerocket-container">';
        $buffer = new Buffer();

        // field settings
        $title = array(
            'label' => 'Page Title'
        );

        $desc = array(
            'label' => 'Search Result Description'
        );

        $og_title = array(
            'label' => 'Title',
            'help'  => 'The open graph protocol is used by social networks like FB, Google+ and Pinterest. Set the title used when sharing.'
        );

        $og_desc = array(
            'label' => 'Description',
            'help'  => 'Set the open graph description to override "Search Result Description". Will be used by FB, Google+ and Pinterest.'
        );

        $img = array(
            'label' => 'Image',
            'help'  => "The image is shown when sharing socially using the open graph protocol. Will be used by FB, Google+ and Pinterest. Need help? Try the Facebook <a href=\"https://developers.facebook.com/tools/debug/og/object/\" target=\"_blank\">open graph object debugger</a> and <a href=\"https://developers.facebook.com/docs/sharing/best-practices\" target=\"_blank\">best practices</a>."
        );

        $tw_img = array(
            'label' => 'Image',
            'help'  => "Images for a 'summary_large_image' card should be at least 280px in width, and at least 150px in height. Image must be less than 1MB in size. Do not use a generic image such as your website logo, author photo, or other image that spans multiple pages."
        );

        $canon = array(
            'label' => 'Canonical URL',
            'help'  => 'The canonical URL that this page should point to, leave empty to default to permalink.'
        );

        $redirect = array(
            'label'    => '301 Redirect',
            'help'     => 'Move this page permanently to a new URL. <a href="#tr_redirect" id="tr_redirect_lock">Unlock 301 Redirect</a>',
            'readonly' => true
        );

        $follow = array(
            'label' => 'Robots Follow?',
            'desc'  => "Don't Follow",
            'help'  => 'This instructs search engines not to follow links on this page. This only applies to links on this page. It\'s entirely likely that a robot might find the same links on some other page and still arrive at your undesired page.'
        );

        $help = array(
            'label' => 'Robots Index?',
            'desc'  => "Don't Index",
            'help'  => 'This instructs search engines not to show this page in its web search results.'
        );

        $tw_help = "Need help? Try the Twitter <a href=\"https://cards-dev.twitter.com/validator/\" target=\"_blank\">card validator</a> and <a href=\"https://dev.twitter.com/cards/getting-started\" target=\"_blank\">getting started guide</a>.";

        // select options
        $follow_opts = array(
            'Not Set'      => 'none',
            'Follow'       => 'follow',
            "Don't Follow" => 'nofollow'
        );

        $index_opts = array(
            'Not Set'     => 'none',
            'Index'       => 'index',
            "Don't Index" => 'noindex'
        );

        $card_opts = array(
            'Summary'             => 'summary',
            'Summary large image' => 'summary_large_image',
            "Photo"               => 'photo',
            "Gallery"             => 'gallery',
            "Product"             => 'product',
            "App"                 => 'app',
            "Player"              => 'player'
        );

        // build form
        /** @var \TypeRocket\Form $form */
        $form = new Form();
        $form->setDebugStatus( false );
        $form->setGroup( '[seo][meta]' );
        $buffer->startBuffer();
        echo $form->text( 'title', array( 'id' => 'tr_title' ), $title );
        echo $form->textarea( 'description', array( 'id' => 'tr_description' ), $desc );
        $buffer->indexBuffer( 'general' ); // index buffer
        $buffer->startBuffer();
        echo $form->text( 'og_title', array(), $og_title );
        echo $form->textarea( 'og_desc', array(), $og_desc );
        echo $form->image( 'meta_img', array(), $img );
        $buffer->indexBuffer( 'social' ); // index buffer
        $buffer->startBuffer();
        echo $form->text( 'tw_site')->setLabel('Twitter account')->setAttribute('placeholder', '@username');
        echo $form->select( 'tw_card')->setOptions($card_opts)->setLabel('Card Type')->setSetting('help', $tw_help);
        echo $form->image( 'tw_img', array(), $tw_img );
        echo $form->text( 'tw_title')->setLabel('Title')->setAttribute('maxlength', 70 );
        echo $form->textarea( 'tw_desc')->setLabel('Description')->setAttribute('maxlength', 200 );
        $buffer->indexBuffer( 'twitter' ); // index buffer
        $buffer->startBuffer();
        echo $form->text( 'canonical', array(), $canon );
        echo $form->text( 'redirect', array( 'readonly' => 'readonly', 'id' => 'tr_redirect' ), $redirect );
        echo $form->select( 'follow', array(), $follow )->setOptions($follow_opts);
        echo $form->select( 'index', array(), $help )->setOptions($index_opts);
        $buffer->indexBuffer( 'advanced' ); // index buffer

        $tabs = new Tabs();
        $tabs->addTab( array(
            'id'       => 'seo-general',
            'title'    => "Basic",
            'content'  => $buffer->getBuffer( 'general' ),
            'callback' => array( $this, 'general' )
        ) )
             ->addTab( array(
                 'id'      => 'seo-social',
                 'title'   => "Social",
                 'content' => $buffer->getBuffer( 'social' )
             ) )
            ->addTab( array(
                'id'      => 'seo-twitter',
                'title'   => "Twitter Cards",
                'content' => $buffer->getBuffer( 'twitter' )
            ) )
             ->addTab( array(
                 'id'      => 'seo-advanced',
                 'title'   => "Advanced",
                 'content' => $buffer->getBuffer( 'advanced' )
             ) )
             ->render();

        echo '</div>';

    }

    public function general()
    {
        global $post; ?>
        <div id="tr-seo-preview" class="control-group">
            <h4>Example Preview</h4>

            <p>Google has <b>no definitive character limits</b> for page "Titles" and "Descriptions". Because of this
                there
                is no way to provide an accurate preview. But, your Google search result may look something like:</p>

            <div class="tr-seo-preview-google">
        <span id="tr-seo-preview-google-title-orig">
          <?php echo substr( strip_tags( $post->post_title ), 0, 59 ); ?>
        </span>
        <span id="tr-seo-preview-google-title">
          <?php
          $title = tr_posts_field( '[seo][meta][title]' );
          if ( ! empty( $title ) ) {
              $s  = strip_tags( $title );
              $tl = strlen( $s );
              echo substr( $s, 0, 59 );
          } else {
              $s  = strip_tags( $post->post_title );
              $tl = strlen( $s );
              echo substr( $s, 0, 59 );
          }

          if ( $tl > 59 ) {
              echo '...';
          }
          ?>
        </span>

                <div id="tr-seo-preview-google-url">
                    <?php echo get_permalink( $post->ID ); ?>
                </div>
        <span id="tr-seo-preview-google-desc-orig">
          <?php echo substr( strip_tags( $post->post_content ), 0, 150 ); ?>
        </span>
        <span id="tr-seo-preview-google-desc">
          <?php
          $desc = tr_posts_field( '[seo][meta][description]' );
          if ( ! empty( $desc ) ) {
              $s  = strip_tags( $desc );
              $dl = strlen( $s );
              echo substr( $s, 0, 150 );
          } else {
              $s  = strip_tags( $post->post_content );
              $dl = strlen( $s );
              echo substr( $s, 0, 150 );
          }

          if ( $dl > 150 ) {
              echo ' ...';
          }
          ?>
        </span>
            </div>
        </div>
    <?php }

}

add_action( 'typerocket_loaded', array( new SeoPlugin(), 'setup' ) );