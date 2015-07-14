<?php
/*
Plugin Name: Pootle Page Builder
Plugin URI: http://pootlepress.com/
Description: pootle page builder helps you create stunning pages with full width rows including parallax background images & videos.
Version: 0.2.1
Author: PootlePress
Author URI: http://pootlepress.com/
License: GPL version 3
*/

/**
 * Pootle Page Builder admin class
 * Class Pootle_Page_Builder_Public
 * @since 0.1.0
 */
final class Pootle_Page_Builder {

	/**
	 * @var Pootle_Page_Builder instance of Pootle_Page_Builder
	 * @access protected
	 * @since 0.1.0
	 */
	protected static $instance;

	/**
	 * @var Pootle_Page_Builder_Admin Admin class instance
	 * @access protected
	 * @since 0.1.0
	 */
	protected $admin;

	/**
	 * @var Pootle_Page_Builder_Public Public class instance
	 * @access protected
	 * @since 0.1.0
	 */
	protected $public;

	/**
	 * Magic __construct
	 * @since 0.1.0
	 */
	public function __construct() {
		$this->constants();
		$this->includes();
		$this->hooks();
	}

	/**
	 * Set the constants
	 * @since 0.1.0
	 */
	private function constants() {
		define( 'POOTLEPB_VERSION', '0.2.1' );
		define( 'POOTLEPB_BASE_FILE', __FILE__ );
		define( 'POOTLEPB_DIR', plugin_dir_path( __FILE__ ) );
		define( 'POOTLEPB_URL', plugin_dir_url( __FILE__ ) );
		// Tracking presence of version older than 3.0.0
		if ( - 1 == version_compare( get_option( 'pootlepb_initial_version' ), '2.5' ) ) {
			define( 'POOTLEPB_OLD_V', get_option( 'pootlepb_initial_version' ) );
		}
	}

	/**
	 * Include the required files
	 * @since 0.1.0
	 */
	private function includes() {

		/** Variables used throughout the plugin */
		require_once POOTLEPB_DIR . 'inc/vars.php';
		/** Functions used throughout the plugin */
		require_once POOTLEPB_DIR . 'inc/funcs.php';
		/** Enhancements and fixes */
		require_once POOTLEPB_DIR . 'inc/enhancements-and-fixes.php';
		/** PPB Admin Class */
		require_once POOTLEPB_DIR . 'inc/class-admin.php';
		/**
		 * PPB Admin Class Instance
		 * @var Pootle_Page_Builder_Admin Instance
		 */
		$GLOBALS['Pootle_Page_Builder_Admin'] = new Pootle_Page_Builder_Admin();
		$this->admin = $GLOBALS['Pootle_Page_Builder_Admin'];
		/** PPB Public Class */
		require_once POOTLEPB_DIR . 'inc/class-public.php';
		/**
		 * PPB Public Class Instance
		 * @var Pootle_Page_Builder_Public Instance
		 */
		$GLOBALS['Pootle_Page_Builder_Public'] = new Pootle_Page_Builder_Public();
		$this->public = $GLOBALS['Pootle_Page_Builder_Public'];
	}

	/**
	 * Adds the actions and filter hooks for plugin functioning
	 * @since 0.1.0
	 */
	private function hooks() {
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
	}

	/**
	 * Hook for activation of Page Builder.
	 * @since 0.1.0
	 */
	public function activate() {
		add_option( 'pootlepb_initial_version', POOTLEPB_VERSION, '', 'no' );

		$current_user = wp_get_current_user();

		//Get first name if set
		$username = '';
		if ( ! empty( $current_user->user_firstname ) ) {
			$username = " {$current_user->user_firstname}";
		}

		$welcome_message = "<b>Hey{$username}! Welcome to Page builder.</b> You're all set to start building stunning pages!<br><a class='button pootle' href='" . admin_url( '/admin.php?page=page_builder' ) . "'>Get started</a>";

		pootlepb_add_admin_notice( 'welcome', $welcome_message, 'updated pootle' );
	}

	/**
	 * Hook for deactivation of Page Builder.
	 * @since 0.1.0
	 */
	public function deactivate() {
		//Get all posts using page builder
		$args  = array(
			'post_type'  => 'page',
			'meta_query' => array(
				array(
					'key'     => 'panels_data',
					'compare' => 'EXISTS',
				),
			)
		);
		$query = new WP_Query( $args );

		foreach ( $query->posts as $post ) {

			//Put pb content in post
			$this->pb_post_content( $post );
		}
	}

	/**
	 * Puts pb content in post content
	 * @param WP_Post $post
	 * @since 0.1.0
	 */
	protected function pb_post_content( $post ) {

		$panel_content = $GLOBALS['Pootle_Page_Builder_Render_Layout']->panels_render( $post->ID );

		global $pootlepb_inline_css;
		$panel_style = '<style>' . $pootlepb_inline_css . '</style>';

		$updated_post = array(
			'ID'           => $post->ID,
			'post_content' => $panel_style . $panel_content,
		);
		wp_update_post( $updated_post );
	}

	/**
	 * Initialize the language files
	 * @action plugins_loaded
	 * @since 0.1.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'ppb-panels', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Enqueue admin scripts and styles
	 * @global $pagenow
	 * @action admin_notices
	 * @since 0.1.0
	 */
	public function enqueue(){
		global $pagenow;

		wp_enqueue_style( 'pootlepage-main-admin', plugin_dir_url( __FILE__ ) . 'css/main-admin.css', array(), POOTLEPB_VERSION );

		if ( $pagenow == 'admin.php' && false !== strpos( filter_input( INPUT_GET, 'page' ), 'page_builder' ) ) {
			wp_enqueue_script( 'jquery-ui-dialog' );
			wp_enqueue_script( 'ppb-settings-script', plugin_dir_url( __FILE__ ) . 'js/settings.js', array( 'jquery-ui-dialog' ) );
			wp_enqueue_style( 'ppb-settings-styles', plugin_dir_url( __FILE__ ) . 'css/settings.css', array() );
			wp_enqueue_style( 'ppb-option-admin', plugin_dir_url( __FILE__ ) . 'css/option-admin.css', array(), POOTLEPB_VERSION );
			wp_enqueue_script( 'ppb-option-admin', plugin_dir_url( __FILE__ ) . 'js/option-admin.js', array( 'jquery' ), POOTLEPB_VERSION );
		}
	}

	/**
	 * Outputs admin notices
	 * @action admin_notices
	 * @since 0.1.0
	 */
	public function admin_notices() {

		$notices = get_option( 'pootlepb_admin_notices', array() );

		delete_option( 'pootlepb_admin_notices' );

		if ( 0 < count( $notices ) ) {
			$html = '';
			foreach ( $notices as $k => $v ) {
				$html .= '<div id="' . esc_attr( $k ) . '" class="fade ' . esc_attr( $v['type'] ) . '">' . wpautop( $v['message'] ) . '</div>' . "\n";
			}
			echo $html;
		}
	}

	/**
	 * Add plugin action links.
	 * @param $links
	 * @action plugin_action_links_$file
	 * @return array
	 * @TODO Use this
	 * @since 0.1.0
	 */
	public function plugin_action_links( $links ) {
		//$links[] = '<a href="http://pootlepress.com/pootle-page-builder/">' . __( 'Support Forum', 'ppb-panels' ) . '</a>';
		//$links[] = '<a href="http://pootlepress.com/page-builder/#newsletter">' . __( 'Newsletter', 'ppb-panels' ) . '</a>';

		return $links;
	}
} //class Pootle_Page_Builder

//Instantiating Pootle_Page_Builder
new Pootle_Page_Builder();
