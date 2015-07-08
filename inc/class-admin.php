<?php

/**
 * Created by PhpStorm.
 * User: shramee
 * Date: 26/6/15
 * Time: 6:39 PM
 * @since 0.1.0
 */
final class Pootle_Page_Builder_Admin extends Pootle_Page_Builder_Abstract {
	/**
	 * @var Pootle_Page_Builder_Admin
	 * @since 0.1.0
	 */
	protected static $instance;

	/**
	 * Magic __construct
	 * @since 0.1.0
	 */
	protected function __construct() {
		$this->includes();
		$this->actions();
	}

	/**
	 * Include the reqd. admin files
	 * @since 0.1.0
	 */
	protected function includes() {

		/** Pootle Page Builder user interface */
		require_once POOTLEPB_DIR . 'inc/class-panels-ui.php';
		/** Content block - Editor panel and output */
		require_once POOTLEPB_DIR . 'inc/class-content-blocks.php';
		/** Take care of styling fields */
		require_once POOTLEPB_DIR . 'inc/styles.php';
		/** Handles PPB meta data *Revisions * */
		require_once POOTLEPB_DIR . 'inc/revisions.php';
		/** More styling */
		require_once POOTLEPB_DIR . 'inc/vantage-extra.php';
	}

	/**
	 * Adds the actions anf filter hooks for plugin functioning
	 * @access protected
	 * @since 0.1.0
	 */
	private function actions() {
		//Adding page builder help tab
		add_action( 'load-page.php', array( $this, 'add_help_tab' ), 12 );
		add_action( 'load-post-new.php', array( $this, 'add_help_tab' ), 12 );

		//Save panel data on post save
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

		//Allow the save post to save panels data
		add_filter( 'pootlepb_save_post_pass', array( $this, 'save_post_or_not' ), 10, 2 );

		//Settings
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'options_init' ) );
		add_action( 'admin_init', array( $this, 'add_new' ) );

		add_action( '', array( $this, '' ) );
	}

	/**
	 * Add a help tab to pages with panels.
	 * @action load-post-new.php, load-page.php
	 * @since 0.1.0
	 */
	public function add_help_tab() {
		$screen = get_current_screen();
		if ( 'post' == $screen->base && in_array( $screen->id, pootlepb_settings( 'post-types' ) ) ) {
			$screen->add_help_tab( array(
				'id'       => 'panels-help-tab', //unique id for the tab
				'title'    => __( 'Page Builder', 'ppb-panels' ), //unique visible title for the tab
				'callback' => array( $this, 'render_help_tab' )
			) );
		}
	}

	/**
	 * Display the content for the help tab.
	 * @TODO Make it more useful
	 * @since 0.1.0
	 */
	public function render_help_tab() {
		echo '<p>';
		_e( 'You can use Pootle Page Builder to create amazing pages, use addons to extend functionality.', 'siteorigin-panels' );
		_e( 'The page layouts are responsive and fully customizable.', 'siteorigin-panels' );
		echo '</p>';
	}

	/**
	 * Save the panels data
	 *
	 * @param $post_id
	 * @param $post
	 *
	 * @action save_post
	 * @since 0.1.0
	 */
	public function save_post( $post_id, $post ) {

		$pass = apply_filters( 'pootlepb_save_post_pass', true, $post );

		if ( empty( $pass ) ) {
			return;
		}

		$panels_data = pootlepb_get_panels_data_from_post();

		if ( function_exists( 'wp_slash' ) ) {
			$panels_data = wp_slash( $panels_data );
		}
		update_post_meta( $post_id, 'panels_data', $panels_data );
	}

	/**
	 * @param bool|null $pass
	 * @param object $post
	 *
	 * @return bool
	 */
	public function save_post_or_not( $pass, $post ) {

		//Check nonce
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, 'pootlepb_nonce' ), 'pootlepb_save' ) ) {
			return false;
		}

		//Check if js was properly loaded
		if ( ! filter_input( INPUT_POST, 'panels_js_complete' ) ) {
			return false;
		}

		//User capability
		if ( ! current_user_can( 'edit_post', $post->id ) ) {
			return false;
		}

		return $pass;

	}

	/**
	 * Add the options page
	 * @since 0.1.0
	 */
	public function admin_menu() {
		add_menu_page( 'Home', 'Page Builder', 'manage_options', 'page_builder', array(
			$this,
			'menu_page',
		), 'dashicons-screenoptions', 26 );
		add_submenu_page( 'page_builder', 'Add New', 'Add New', 'manage_options', 'page_builder_add', array(
			$this,
			'menu_page',
		) );
		add_submenu_page( 'page_builder', 'Settings', 'Settings', 'manage_options', 'page_builder_settings', array(
			$this,
			'menu_page',
		) );
		add_submenu_page( 'page_builder', 'Add-ons', 'Add-ons', 'manage_options', 'page_builder_addons', array(
			$this,
			'menu_page',
		) );
	}

	/**
	 * Register all the settings fields.
	 * @since 0.1.0
	 */
	public function options_init() {
		register_setting( 'pootlepage-add-ons', 'pootlepb_add_ons' );
		register_setting( 'pootlepage-display', 'siteorigin_panels_display', array(
			$this,
			'pootlepb_options_sanitize_display',
		) );

		add_settings_section( 'display', __( 'Display', 'ppb-panels' ), '__return_false', 'pootlepage-display' );

		// The display fields
		add_settings_field( 'responsive', __( 'Responsive', 'ppb-panels' ), array(
			$this,
			'options_field_generic',
		), 'pootlepage-display', 'display', array( 'type' => 'responsive' ) );
		add_settings_field( 'mobile-width', __( 'Mobile Width', 'ppb-panels' ), array(
			$this,
			'options_field_generic',
		), 'pootlepage-display', 'display', array( 'type' => 'mobile-width' ) );
	}

	/**
	 * Display the admin page.
	 * @since 0.1.0
	 */
	public function menu_page() {

		//Replace prefix for submenu pages
		$inc_file = str_replace( 'page_builder_', '', filter_input( INPUT_GET, 'page' ) );

		//Replace main menu page with welcome
		$inc_file = str_replace( 'page_builder', 'welcome', $inc_file );

		include POOTLEPB_DIR . "tpl/$inc_file.php";
	}

	/**
	 * Redirecting for Page Builder > Add New option
	 * @since 0.1.0
	 */
	public function add_new() {
		global $pagenow;

		if ( 'admin.php' == $pagenow && 'page_builder_add' == filter_input( INPUT_GET, 'page' ) ) {
			header( 'Location: ' . admin_url( '/post-new.php?post_type=page&page_builder=pootle' ) );
			die();
		}
	}

	/**
	 * Output settings field
	 *
	 * @param array $args
	 * @param string $groupName
	 *
	 * @since 0.1.0
	 */
	public function options_field_generic( $args, $groupName = 'siteorigin_panels_display' ) {
		$settings = pootlepb_settings();
		switch ( $args['type'] ) {
			case 'responsive' :
				?><label><input type="checkbox"
				                name="<?php echo esc_attr( $groupName ) ?>[<?php echo esc_attr( $args['type'] ) ?>]" <?php checked( $settings[ $args['type'] ] ) ?>
				                value="1"/> <?php _e( 'Enabled', 'ppb-panels' ) ?></label><?php
				break;
			case 'mobile-width' :
				?><input type="text" name="<?php echo esc_attr( $groupName ) ?>[<?php echo esc_attr( $args['type'] ) ?>]"
				         value="<?php echo esc_attr( $settings[ $args['type'] ] ) ?>"
				         class="small-text" /> <?php _e( 'px', 'ppb-panels' ) ?><?php
				break;
		}

		if ( ! empty( $args['description'] ) ) {
			?><p class="description"><?php echo esc_html( $args['description'] ) ?></p><?php
		}
	}

	/**
	 * Sanitize display options
	 *
	 * @param $vals
	 *
	 * @return mixed
	 * @since 0.1.0
	 */
	public function pootlepb_options_sanitize_display( $vals ) {

		//Enable Responsive media queries
		$vals['responsive']      = ! empty( $vals['responsive'] );

		return $vals;
	}
}