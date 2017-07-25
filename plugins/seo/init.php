<?php
class TypeRocketSeoPlugin
{

    public $itemId = null;

    public function __construct()
    {
        if ( ! function_exists( 'add_action' ) ) {
            echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
            exit;
        }

        add_filter( 'jetpack_enable_opengraph', '__return_false', 99 );
    }

    public function setup()
    {
        if ( ! defined( 'WPSEO_URL' ) && ! defined( 'AIOSEOP_VERSION' ) ) {
            define( 'TR_SEO', '1.0' );
            add_action('tr_model', [$this, 'fillable'], 9999999999, 2 );
            add_action( 'wp_head', [$this, 'head_data'], 0 );
            add_action( 'template_redirect', [$this, 'loaded'], 0 );
            add_filter( 'document_title_parts', [$this, 'title'], 100, 3 );
            remove_action( 'wp_head', 'rel_canonical' );
            add_action( 'wp', [$this, 'redirect'], 99, 1 );

            if ( is_admin() ) {
                add_action( 'add_meta_boxes', [$this, 'seo_meta']);
            }
        }
    }

    public function fillable( $model )
    {
        global $post;

        if($model instanceof \TypeRocket\Models\WPPost) {
            $fillable = $model->getFillableFields();
            /** @var \WP_Post $data */
            $types = get_post_types(['public' => true]);
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
        $publicTypes = get_post_types( ['public' => true]);
        $args        = [
            'label'    => __('Search Engine Optimization'),
            'priority' => 'low',
            'callback' => [$this, 'meta']
        ];
        $obj         = new \TypeRocket\Register\MetaBox( 'tr_seo', null, $args );
        $obj->addPostType( $publicTypes )->register();
    }

    // Page Title
    public function title( $title, $arg2 = null, $arg3 = null )
    {
        $newTitle = trim(tr_posts_field( 'seo.meta.title', $this->itemId ));

        if ( !empty($newTitle) ) {
            return [$newTitle];
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
        $seo              = tr_posts_field('seo.meta', $object_id);
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

            $robot_data = mb_substr( $robot_data, 0, - 2 );
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

            if (mb_substr($src, 0, mb_strlen($prefix)) == $prefix) {
                $src = mb_substr($src, mb_strlen($prefix));
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
            $redirect = tr_posts_field( 'seo.meta.redirect', $this->itemId );
            if ( ! empty( $redirect ) ) {
                wp_redirect( $redirect, 301 );
                exit;
            }
        }
    }

    public function meta()
    {
        echo '<div class="typerocket-container">';

        // build form
        $form = new \TypeRocket\Elements\Form();
        $form->setDebugStatus( false );
        $form->setGroup( 'seo.meta' );
        $seo_plugin = $this;

        // General
        $general = function() use ($form, $seo_plugin){

            $title = [
                'label' => __('Page Title')
            ];

            $desc = [
                'label' => __('Search Result Description')
            ];

            echo $form->text( 'title', ['id' => 'tr_title'], $title );
            echo $form->textarea( 'description', ['id' => 'tr_description'], $desc );

            $seo_plugin->general();
        };

        // Social
        $social = function() use ($form){

            $og_title = [
                'label' => __('Title'),
                'help'  => __('The open graph protocol is used by social networks like FB, Google+ and Pinterest. Set the title used when sharing.')
            ];

            $og_desc = [
                'label' => __('Description'),
                'help'  => __('Set the open graph description to override "Search Result Description". Will be used by FB, Google+ and Pinterest.')
            ];

            $img = [
                'label' => __('Image'),
                'help'  => __("The image is shown when sharing socially using the open graph protocol. Will be used by FB, Google+ and Pinterest. Need help? Try the Facebook <a href=\"https://developers.facebook.com/tools/debug/og/object/\" target=\"_blank\">open graph object debugger</a> and <a href=\"https://developers.facebook.com/docs/sharing/best-practices\" target=\"_blank\">best practices</a>.")
            ];

            echo $form->text( 'og_title', [], $og_title );
            echo $form->textarea( 'og_desc', [], $og_desc );
            echo $form->image( 'meta_img', [], $img );
        };

        // Twitter
        $twitter = function() use ($form){

            $tw_img = [
                'label' => __('Image'),
                'help'  => __("Images for a 'summary_large_image' card should be at least 280px in width, and at least 150px in height. Image must be less than 1MB in size. Do not use a generic image such as your website logo, author photo, or other image that spans multiple pages.")
            ];

            $tw_help = __("Need help? Try the Twitter <a href=\"https://cards-dev.twitter.com/validator/\" target=\"_blank\">card validator</a>, <a href=\"https://dev.twitter.com/cards/getting-started\" target=\"_blank\">getting started guide</a>, and <a href=\"https://business.twitter.com/en/help/campaign-setup/advertiser-card-specifications.html\" target=\"_blank\">advertiser creative specifications</a>.");

            $card_opts = [
                __('Summary')             => 'summary',
                __('Summary large image') => 'summary_large_image',
            ];

            echo $form->text( 'tw_site')->setLabel('Twitter account')->setAttribute('placeholder', '@username');
            echo $form->select( 'tw_card')->setOptions($card_opts)->setLabel('Card Type')->setSetting('help', $tw_help);
            echo $form->text( 'tw_title')->setLabel('Title')->setAttribute('maxlength', 70 );
            echo $form->textarea( 'tw_desc')->setLabel('Description')->setHelp( __('Description length is dependent on card type.') );
            echo $form->image( 'tw_img', [], $tw_img );
        };

        // Advanced
        $advanced = function() use ($form){

            $redirect = [
                'label'    => __('301 Redirect'),
                'help'     => __('Move this page permanently to a new URL.') . '<a href="#tr_redirect" id="tr_redirect_lock">' . __('Unlock 301 Redirect') .'</a>',
                'readonly' => true
            ];

            $follow = [
                'label' => __('Robots Follow?'),
                'desc'  => __("Don't Follow"),
                'help'  => __('This instructs search engines not to follow links on this page. This only applies to links on this page. It\'s entirely likely that a robot might find the same links on some other page and still arrive at your undesired page.')
            ];

            $follow_opts = [
                __('Not Set')      => 'none',
                __('Follow')       => 'follow',
                __("Don't Follow") => 'nofollow'
            ];

            $index_opts = [
                __('Not Set')     => 'none',
                __('Index')       => 'index',
                __("Don't Index") => 'noindex'
            ];

            $canon = [
                'label' => __('Canonical URL'),
                'help'  => __('The canonical URL that this page should point to, leave empty to default to permalink.')
            ];

            $help = [
                'label' => __('Robots Index?'),
                'desc'  => __("Don't Index"),
                'help'  => __('This instructs search engines not to show this page in its web search results.')
            ];

            echo $form->text( 'canonical', [], $canon );
            echo $form->text( 'redirect', ['readonly' => 'readonly', 'id' => 'tr_redirect'], $redirect );
            echo $form->row([
                $form->select( 'follow', [], $follow )->setOptions($follow_opts),
                $form->select( 'index', [], $help )->setOptions($index_opts)
            ]);
        };

        $tabs = new \TypeRocket\Elements\Tabs();
        $tabs->addTab( [
                'id'       => 'seo-general',
                'title'    => __("Basic"),
                'callback' => $general
            ])
            ->addTab( [
                 'id'      => 'seo-social',
                 'title'   => __("Social"),
                 'callback' => $social
             ])
            ->addTab( [
                'id'      => 'seo-twitter',
                'title'   => __("Twitter Cards"),
                'callback' => $twitter
            ])
            ->addTab( [
                 'id'      => 'seo-advanced',
                 'title'   => __("Advanced"),
                 'callback' => $advanced
             ])
            ->render();

        echo '</div>';

    }

    public function general()
    {
        global $post; ?>
        <div id="tr-seo-preview" class="control-group">
            <h4><?php _e('Example Preview'); ?></h4>

            <p><?php _e('Google has <b>no definitive character limits</b> for page "Titles" and "Descriptions". Because of this
                there is no way to provide an accurate preview. But, your Google search result may look something like:'); ?>

            <div class="tr-seo-preview-google">
        <span id="tr-seo-preview-google-title-orig">
          <?php echo mb_substr( strip_tags( $post->post_title ), 0, 59 ); ?>
        </span>
        <span id="tr-seo-preview-google-title">
          <?php
          $title = tr_posts_field( 'seo.meta.title' );
          if ( ! empty( $title ) ) {
              $s  = strip_tags( $title );
              $tl = mb_strlen( $s );
              echo mb_substr( $s, 0, 59 );
          } else {
              $s  = strip_tags( $post->post_title );
              $tl = mb_strlen( $s );
              echo mb_substr( $s, 0, 59 );
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
          <?php echo mb_substr( strip_tags( $post->post_content ), 0, 150 ); ?>
        </span>
        <span id="tr-seo-preview-google-desc">
          <?php
          $desc = tr_posts_field( 'seo.meta.description' );
          if ( ! empty( $desc ) ) {
              $s  = strip_tags( $desc );
              $dl = mb_strlen( $s );
              echo mb_substr( $s, 0, 150 );
          } else {
              $s  = strip_tags( $post->post_content );
              $dl = mb_strlen( $s );
              echo mb_substr( $s, 0, 150 );
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

add_action( 'typerocket_loaded', [new TypeRocketSeoPlugin(), 'setup']);
