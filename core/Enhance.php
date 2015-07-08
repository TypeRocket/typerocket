<?php
namespace TypeRocket;

class Enhance {

	/**
	 * Actions
	 *
	 * post_updated_messages
	 */
	public function run() {
		add_action( 'post_updated_messages', array( $this, 'set_messages' ) );
		add_action( 'edit_user_profile', array( $this, 'user_content' ) );
		add_action( 'show_user_profile', array( $this, 'user_content' ) );
		add_action( 'admin_init', array( $this, 'add_css' ) );
		add_action( 'admin_init', array( $this, 'add_js' ) );
	}

	/**
	 * Set custom post type messages to make more since.
	 *
	 * @param $messages
	 *
	 * @return mixed
	 */
	function set_messages( $messages ) {
		global $post;

		$pt = get_post_type( $post->ID );

		if ( $pt != 'attachment' && $pt != 'page' && $pt != 'post' ) :

			$obj      = get_post_type_object( $pt );
			$singular = $obj->labels->singular_name;

			if ( $obj->public == true ) :
				$view    = sprintf( __( '<a href="%s">View %s</a>' ), esc_url( get_permalink( $post->ID ) ),
					$singular );
				$preview = sprintf( __( '<a target="_blank" href="%s">Preview %s</a>' ),
					esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ), $singular );
			else :
				$view = $preview = '';
			endif;

			$messages[ $pt ] = array(
				1  => sprintf( __( '%s updated. %s' ), $singular, $view ),
				2  => __( 'Custom field updated.' ),
				3  => __( 'Custom field deleted.' ),
				4  => sprintf( __( '%s updated.' ), $singular ),
				5  => isset( $_GET['revision'] ) ? sprintf( __( '%s restored to revision from %s' ), $singular,
					wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				6  => sprintf( __( '%s published. %s' ), $singular, $view ),
				7  => sprintf( __( '%s saved.' ), $singular ),
				8  => sprintf( __( '%s submitted. %s' ), $singular, $preview ),
				9  => sprintf( __( '%s scheduled for: <strong>%1$s</strong>. %s' ), $singular,
					date_i18n( 'M j, Y @ G:i', strtotime( $post->post_date ) ), $preview ),
				10 => sprintf( __( '%s draft updated. ' ), $singular ),
			);

		endif;

		return $messages;
	}

	public function add_css() {
		if ( is_admin() ) {
			$paths = \TypeRocket\Config::getPaths();
			wp_enqueue_style( 'typerocket-tr-icons', $paths['urls']['assets'] . '/css/tr-icons.css' );
			wp_enqueue_style( 'typerocket-styles', $paths['urls']['assets'] . '/css/typerocket.css' );
			wp_enqueue_style( 'typerocket-tabs', $paths['urls']['assets'] . '/css/tabs.css' );
		}
	}

	public function add_js() {
		if ( is_admin() ) {
			$paths = \TypeRocket\Config::getPaths();

			if ( TR_DEBUG === true ) {
				wp_enqueue_script( 'typerocket-dev', $paths['urls']['assets'] . '/js/dev.js', array( 'jquery' ), '1.0',
					true );
			}
			wp_enqueue_script( 'typerocket-scripts', $paths['urls']['assets'] . '/js/typerocket.js', array( 'jquery' ),
				'1.0', true );
			wp_enqueue_script( 'typerocket-scripts-global', $paths['urls']['assets'] . '/js/global.js', array(),
				'1.0' );

			// Still working on a fix
			// wp_enqueue_script( 'typerocket-wp_editor', tr::$paths['urls']['assets'] . '/js/wp_editor.js', array('jquery'), '1.0', true );

		}
	}

	public function user_content( $user_obj ) {
		echo '<div class="typerocket-container typerocket-wp-style-guide">';
		do_action( 'tr_user_profile', $user_obj );
		echo '</div>';
	}

}