<?php
namespace TypeRocket;

class SeoPlugin
{

    public $item_id = null;

    function __construct() {
        if ( !function_exists( 'add_action' ) ) {
            echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
            exit;
        }
    }

    function setup()
    {
        if ( ! defined( 'WPSEO_URL' ) && ! defined( 'AIOSEOP_VERSION' )) {
            define( 'TR_SEO', '1.0' );
            add_action( 'admin_init', array( $this, 'css' ) );
            add_action( 'wp_head', array( $this, 'head_data' ), 0 );
            add_action( 'template_redirect', array( $this, 'loaded' ), 0 );
            add_action( 'add_meta_boxes', array( $this, 'seo_meta' ) );
            add_filter( 'wp_title', array( $this, 'title' ), 100, 3 );
            remove_action( 'wp_head', 'rel_canonical' );
            add_action( 'wp', array( $this, 'redirect' ), 99, 1 );
        }
    }

    function loaded() {
        $this->item_id = get_queried_object_id();
    }

    function seo_meta()
    {
        $publicTypes = get_post_types( array( 'public' => true ) );
        $obj         = new Metabox();
        $obj->make( 'tr_seo',
            array( 'label' => 'Search Engine Optimization', 'priority' => 'low', 'callback' => array($this, 'meta') ) )->apply( $publicTypes )->bake();
    }

    // Page Title
    function title( $title, $sep = '', $other = '' )
    {
        $newTitle = tr_post_field( '[seo][meta][title]', $this->item_id );

        if ($newTitle != null) {
            return $newTitle;
        } else {
            return $title;
        }

    }

    function title_tag() {
        echo '<title>' . $this->title( '|', false, 'right' ) . "</title>\n";
    }

    // head meta data
    function head_data()
    {
        $object_id = $this->item_id;

        // meta vars
        $desc             = esc_attr( tr_post_field( '[seo][meta][description]', $object_id ) );
        $og_title         = esc_attr( tr_post_field( '[seo][meta][og_title]', $object_id ) );
        $og_desc          = esc_attr( tr_post_field( '[seo][meta][og_desc]', $object_id ) );
        $img              = esc_attr( tr_post_field( '[seo][meta][meta_img]', $object_id ) );
        $canon            = esc_attr( tr_post_field( '[seo][meta][canonical]', $object_id ) );
        $robots['index']  = esc_attr( tr_post_field( '[seo][meta][index]', $object_id ) );
        $robots['follow'] = esc_attr( tr_post_field( '[seo][meta][follow]', $object_id ) );

        // Extra
        if ( ! empty( $canon )) {
            echo "<link rel=\"canonical\" href=\"{$canon}\" />";
        } else {
            rel_canonical();
        }

        // Robots
        if ( ! empty( $robots )) {
            $robot_data = '';
            foreach ($robots as $value) {
                if ( ! empty( $value )) {
                    $robot_data .= $value . ', ';
                }
            }

            $robot_data = substr( $robot_data, 0, - 2 );
            if ( ! empty( $robot_data )) {
                echo "<meta name=\"robots\" content=\"{$robot_data}\" />";
            }
        }

        // OG
        if ( ! empty( $og_title )) {
            echo "<meta property=\"og:title\" content=\"{$og_title}\" />";
        }
        if ( ! empty( $og_desc )) {
            echo "<meta property=\"og:description\" content=\"{$og_desc}\" />";
        }
        if ( ! empty( $img )) {
            echo "<meta property=\"og:image\" content=\"{$img}\" />";
        }

        // Basic
        if ( ! empty( $desc )) {
            echo "<meta name=\"Description\" content=\"{$desc}\" />";
        }
    }

    // 301 Redirect
    function redirect()
    {
        if (is_singular()) {
            $redirect = tr_post_field( '[seo][meta][redirect]', $this->item_id );
            if ( ! empty( $redirect )) {
                wp_redirect( $redirect, 301 );
                exit;
            }
        }
    }

    // CSS
    function css()
    {
        $paths = Config::getPaths();
        $path  = $paths['urls']['plugins'] . '/seo/';
        wp_enqueue_style( 'tr-seo', $path . 'style.css' );
        wp_enqueue_script( 'tr-seo', $path . 'script.js', array( 'jquery' ), '1.0', true );
    }

    function meta()
    {
        echo '<div class="typerocket-container">';
        $utility = new Buffer();

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
            'help'  => 'The image is shown when sharing socially using the open graph protocol. Will be used by FB, Google+ and Pinterest.'
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

        // select options
        $follow_opts = array(
            'Not Set'      => '',
            'Follow'       => 'follow',
            "Don't Follow" => 'nofollow'
        );

        $index_opts = array(
            'Not Set'     => '',
            'Index'       => 'index',
            "Don't Index" => 'noindex'
        );

        // build form
        /** @var \TypeRocket\Form $form */
        $form = new Form();
        $form->setDebugStatus(false);
        $form->setGroup('[seo][meta]');
        $form->make();
        $utility->startBuffer();
        $form->text( 'title', array( 'id' => 'tr_title' ), $title )
             ->textarea( 'description', array( 'id' => 'tr_description' ), $desc );
        $utility->indexBuffer( 'general' ); // index buffer
        $utility->startBuffer();
        $form->text( 'og_title', array(), $og_title )
             ->textarea( 'og_desc', array(), $og_desc )
             ->image( 'meta_img', array(), $img );
        $utility->indexBuffer( 'social' ); // index buffer
        $utility->startBuffer();
        $form->text( 'canonical', array(), $canon )
             ->text( 'redirect', array( 'readonly' => 'readonly', 'id' => 'tr_redirect' ), $redirect )
             ->select( 'follow', $follow_opts, array(), $follow )
             ->select( 'index', $index_opts, array(), $help );
        $utility->indexBuffer( 'extra' ); // index buffer

        $tabs = new Layout();
        $tabs->add_tab( array(
            'id'       => 'seo-general',
            'title'    => "Basic",
            'content'  => $utility->getBuffer('general'),
            'callback' => array($this, 'general')
        ) )
             ->add_tab( array(
                 'id'      => 'seo-social',
                 'title'   => "OG",
                 'content' => $utility->getBuffer('social')
             ) )
             ->add_tab( array(
                 'id'      => 'seo-extra',
                 'title'   => "Extras",
                 'content' => $utility->getBuffer('extra')
             ) )
             ->make( 'meta' );

        echo '</div>';

    }

    function general()
    {
        global $post; ?>
        <div id="tr-seo-preview" class="control-group">
            <h4>Example Preview</h4>

            <p>Google has <b>no definitive character limits</b> for page "Titles" and "Descriptions". Because of this there
                is no way to provide an accurate preview. But, your Google search result may look something like:</p>

            <div class="tr-seo-preview-google">
        <span style="display: none" id="tr-seo-preview-google-title-orig">
          <?php echo substr( strip_tags( $post->post_title ), 0, 59 ); ?>
        </span>
        <span id="tr-seo-preview-google-title">
          <?php
          $title = tr_post_field( '[seo][meta][title]' );
          if ( ! empty( $title )) {
              $s  = strip_tags( $title );
              $tl = strlen( $s );
              echo substr( $s, 0, 59 );
          } else {
              $s  = strip_tags( $post->post_title );
              $tl = strlen( $s );
              echo substr( $s, 0, 59 );
          }

          if ($tl > 59) {
              echo '...';
          }
          ?>
        </span>

        <div id="tr-seo-preview-google-url">
            <?php echo get_permalink( $post->ID ); ?>
        </div>
        <span style="display: none" id="tr-seo-preview-google-desc-orig">
          <?php echo substr( strip_tags( $post->post_content ), 0, 150 ); ?>
        </span>
        <span id="tr-seo-preview-google-desc">
          <?php
          $desc = tr_post_field( '[seo][meta][description]' );
          if ( ! empty( $desc )) {
              $s  = strip_tags( $desc );
              $dl = strlen( $s );
              echo substr( $s, 0, 150 );
          } else {
              $s  = strip_tags( $post->post_content );
              $dl = strlen( $s );
              echo substr( $s, 0, 150 );
          }

          if ($dl > 150) {
              echo ' ...';
          }
          ?>
        </span>
            </div>
        </div>
    <?php }

}

$tr_seo = new SeoPlugin();
add_action( 'typerocket_loaded', array( $tr_seo, 'setup' ) );
unset( $tr_seo );