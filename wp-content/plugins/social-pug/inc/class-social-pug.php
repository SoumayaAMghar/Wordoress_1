<?php

use Mediavine\Grow\Settings;
use Mediavine\Grow\Share_Counts;
use Mediavine\Grow\Status_API_Controller;

/**
 * Core plugin class.
 */
class Social_Pug {

	public const API_NAMESPACE = 'mv-grow-social/v1';

	/** @var string|null Build tool sets this. */
	const VERSION = '1.20.1';

	/** @var string|null Version number for this release. @deprecated Use MV_GROW_VERSION */
	public static $VERSION;

	private static $instance = null;

	/** @var Status_API_Controller */
	private $status_api_controller;

	/** @var \Mediavine\Grow\Asset_Loader  */
	public $asset_loader = null;

	/** @var \Mediavine\Grow\Frontend_Data */
	public $frontend_data = null;

	/** @var \Mediavine\Grow\Admin_Notices */
	public $admin_notices = null;

	/** @var \Mediavine\Grow\Settings_API */
	public $settings_api = null;

	/** @var \Mediavine\Grow\Networks */
	public $networks = null;

	/**  @var \Mediavine\Grow\Icons */
	public $icons = null;

	/**  @var \Mediavine\Grow\Subscribe_Widget */
	public $subscribe_widget = null;

	/** @var \Mediavine\Grow\Tools\Toolkit Container for all the tools. */
	public $tools = null;

	/** @var Share_Counts|null Count class. */
	public $share_counts = null;

	/** @var string $has_license Whether or not there is a license */
	public $has_license = false;

	/**
	 * Get the defined addon version, if available.
	 *
	 * @return string|null
	 */
	public function get_version() : ?string {
		$version = defined( 'MV_GROW_VERSION' ) ? MV_GROW_VERSION : null;
		return $version;
	}

	/**
	 * Determine our version number depending on whether plugin has been built or is in development.
	 */
	public function set_version() {
		if ( ! is_null( self::VERSION ) ) {
			// If the build tool has run, use its version.
			self::$VERSION = self::VERSION; // @codingStandardsIgnoreLine
			define( 'MV_GROW_VERSION', self::VERSION );
			return;
		}
		// Pull version from the plugin bootstrap file
		$version = \get_file_data(DPSP_PLUGIN_DIR . '/index.php', [
			'version' => 'Version',
		])['version'];

		$version = ! empty( $version ) ? $version : '99';
		self::$VERSION = $version; // @codingStandardsIgnoreLine
		define( 'MV_GROW_VERSION', $version );
	}

	/**
	 * Singleton factory.
	 *
	 * @return Social_Pug|null
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Plugin bootstrap.
	 */
	public function init() {
		$this->set_version();
		define( 'DPSP_VERSION', self::$VERSION );

		define( 'DPSP_TRANSLATION_TEXTDOMAIN', 'social-pug' );

		$this->has_license = (bool) Settings::get_setting( 'mv_grow_license', false );

		// Register feature flags early.
		add_action( 'after_setup_theme', '\Mediavine\Grow\register_flags' );

		// Setup compatibility hooks.
		add_action( 'wp_head', [ 'Mediavine\Grow\Compatibility', 'disable_known_meta_tags' ], 1 );
		add_action( 'wp', [ 'Mediavine\Grow\Compatibility', 'set_yoast_meta_data' ], 10 );
		add_action( 'mv_grow_meta_tag_hook', [ 'Mediavine\Grow\Compatibility', 'set_yoast_meta_tag_hook' ], 10 );

		$this->settings_api = \Mediavine\Grow\Settings_API::get_instance();

		/**
		 * Hook in when WordPress is preparing to serve an API request.
		 *
		 * @param WP_REST_Server $wp_rest_server Server object.
		 */
		add_action( 'rest_api_init', function ( $wp_rest_server ) {
			$this->status_api_controller = new Mediavine\Grow\Status_API_Controller( $wp_rest_server, $this );
			$this->status_api_controller->register_routes( $wp_rest_server );
		} );

		$this->setup_integrations();
		$this->setup_free_tools();

		$this->asset_loader     = \Mediavine\Grow\Asset_Loader::get_instance();
		$this->frontend_data    = \Mediavine\Grow\Frontend_Data::get_instance();
		$this->networks         = \Mediavine\Grow\Networks::get_instance();
		$this->icons            = \Mediavine\Grow\Icons::get_instance();
		$this->subscribe_widget = \Mediavine\Grow\Subscribe_Widget::get_instance();

		// Meta tags
		add_action( apply_filters( 'mv_grow_meta_tag_hook', 'wp_head' ), [ 'Mediavine\Grow\Meta_Tags', 'build_and_output' ], 1 );

		// Activation & deativation hooks.
		register_activation_hook( mv_grow_get_activation_path(), 'dpsp_default_settings' );
		register_activation_hook( mv_grow_get_activation_path(), 'dpsp_set_cron_jobs' );
		register_deactivation_hook( mv_grow_get_activation_path(), 'dpsp_stop_cron_jobs' );

		add_action( 'init', [ $this, 'init_translation' ] );
		add_action( 'admin_menu', [ $this, 'add_main_menu_page' ], 10 );
		add_action( 'admin_menu', [ $this, 'remove_main_menu_page' ], 25 );
		add_action( 'admin_enqueue_scripts', [ $this, 'init_admin_scripts' ], 100 );
		add_action( 'wp_enqueue_scripts', [ $this->asset_loader, 'register_front_end_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this->asset_loader, 'enqueue_scripts' ] );
		add_action( 'wp_footer', [ $this->asset_loader, 'maybe_dequeue' ] );
		add_action( 'admin_init', [ $this, 'update_database' ] );
		add_filter( 'body_class', [ $this, 'add_body_class' ] );

		// Set up Facebook Authorization
		add_action( 'admin_init', 'dpsp_capture_authorize_facebook_access_token' );
		// Add a class to the admin body to tell plugin pages apart
		add_filter( 'admin_body_class', [ $this, 'admin_body_class' ] );

		add_filter( 'plugin_action_links_' . MV_GROW_PLUGIN_BASENAME, [ $this, 'add_plugin_action_links' ] );

		// Not sure why this is in a hook, so I'm leaving it for now, but this should be looked into.
		// TODO: It's also in the regular `init` hook, so not sure why it's called `load_resources_admin` - STA
		add_action( 'init', [ $this, 'load_resources_admin' ] );

		// Hook registration in functions files.
		dpsp_register_functions();
		dpsp_register_functions_admin();
		dpsp_register_functions_cron();
		dpsp_register_functions_post();

		if ( Share_Counts::are_counts_enabled() ) {
			// Only Register Count functions if counts are enabled
			$this->share_counts = Share_Counts::get_instance();
		}

		dpsp_register_functions_tools();

		// Hook registration in tools files.
		dpsp_register_floating_sidebar();
		dpsp_register_inline_content();

		// Hook registration in admin files.
		dpsp_register_admin_metaboxes();
		dpsp_register_admin_widgets();
		dpsp_register_admin_debugger();
		dpsp_register_admin_settings();
		dpsp_register_admin_toolkit();

		// Version-specific feature registration.
		if ( class_exists( '\Mediavine\Grow\Shortcodes' ) && ! self::is_free() ) {
			$this->register_pro_features();
		} else {
			$this->register_free_features();
		}

		// This must happen after register_free_features() otherwise pro notices will show up on free
		$this->admin_notices = \Mediavine\Grow\Admin_Notices::get_instance();
	}

	/**
	 * Register Pro-only features.
	 */
	public function register_pro_features() {
		dpsp_register_functions_version_update();

		\Mediavine\Grow\Shortcodes::register_shortcodes();
		\Mediavine\Grow\Activation::get_instance();
		\Mediavine\Grow\Data_Sync::get_instance();

		$this->setup_pro_tools();

		add_action( 'admin_init', 'Mediavine\Grow\Intercom::get_instance' );

		// Register Gutenberg editor assets
		add_action( 'enqueue_block_editor_assets', [ $this, 'init_gutenberg_scripts' ] );

		dpsp_register_follow_widget();
		dpsp_register_import_export();

		dpsp_register_link_shortening();
		dpsp_register_link_shortening_bitly();
		dpsp_register_link_shortening_branch();

		dpsp_register_social_shares_recovery();
		dpsp_register_utm_tracking();
		dpsp_register_click_tweet();

		dpsp_register_images_pinterest();
		dpsp_register_pop_up();
		dpsp_register_sticky_bar();
	}

	/**
	 * Register Free-only features.
	 */
	public function register_free_features() {
		add_action( 'dpsp_enqueue_admin_scripts', 'dpsp_enqueue_admin_scripts_feedback' );
		//add_action( 'admin_footer', 'dpsp_output_feedback_form' );
		//add_action( 'wp_ajax_dpsp_ajax_send_feedback', 'dpsp_ajax_send_feedback' );
		add_action( 'dpsp_submenu_page_bottom', 'dpsp_add_submenu_page_sidebar' );
		add_action( 'admin_menu', 'dpsp_register_extensions_subpage', 102 );
		add_filter( 'mv_grow_is_free', '__return_true' );
	}

	/**
	 * Integrations bootstrap.
	 */
	public function setup_integrations() {
		$integration_container = \Mediavine\Grow\Integrations\Container::get_instance();
		$integration_container->add_integrations(
			[
				\Mediavine\Grow\Integrations\MV_Trellis::get_instance(),
				\Mediavine\Grow\Integrations\MV_Create::get_instance(),
			]
		);
	}

	/**
	 *  Register all tool classes with the main class
	 */
	public function setup_pro_tools() {
		$tool_container = \Mediavine\Grow\Tools\Toolkit::get_instance();
		$tools          = [
			new \Mediavine\Grow\Tools\Pop_Up(),
			new \Mediavine\Grow\Tools\Pinterest(),
			new \Mediavine\Grow\Tools\Floating_Sidebar(),
			new \Mediavine\Grow\Tools\Import_Export(),
			new \Mediavine\Grow\Tools\Follow_Widget(),
			new \Mediavine\Grow\Tools\Sticky_Bar(),
		];
		$tool_container->add( $tools );
		foreach ( $tools as $tool ) {
			$this->settings_api->register_setting( $tool );
		}
		$this->tools = $tool_container;
	}

	/**
	 * Register all tool classes available with free version
	 */
	public function setup_free_tools() {
		$tool_container = \Mediavine\Grow\Tools\Toolkit::get_instance();
		$tools          = [
			new \Mediavine\Grow\Tools\Inline_Content(),
			new \Mediavine\Grow\Tools\Floating_Sidebar(),
		];
		$tool_container->add( $tools );
		foreach ( $tools as $tool ) {
			$this->settings_api->register_setting( $tool );
		}
		$this->tools = $tool_container;
	}

	public static function assets_url() {
		return plugin_dir_url( __FILE__ );
	}

	public function add_body_class( $body_classes ) {
		$active_tools = Mediavine\Grow\Settings::get_setting( 'dpsp_active_tools' );
		if ( in_array( 'share_sidebar', $active_tools, true ) && ! in_array( 'has_grow_sidebar', $body_classes, true ) ) {
			$body_classes[]   = 'has-grow-sidebar';
			$sidebar_settings = Mediavine\Grow\Settings::get_setting( 'dpsp_location_sidebar', 'not_set' );
			if ( isset( $sidebar_settings['display']['show_mobile'] ) ) {
				$body_classes[] = 'has-grow-sidebar-mobile';
			}
		}

		return $body_classes;
	}

	/**
	 * Loads the translations files if they exist
	 *
	 */
	public function init_translation() {

		load_plugin_textdomain( DPSP_TRANSLATION_TEXTDOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/translations' );

	}

	/**
	 * Add the main menu page
	 *
	 */
	public function add_main_menu_page() {

		add_menu_page( __( 'Grow Social by Mediavine', 'social-pug' ), __( 'Grow', 'social-pug' ), 'manage_options', 'dpsp-social-pug', '', 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDI0LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4IgoJIHZpZXdCb3g9IjAgMCAyMCAyMCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjAgMjA7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPHN0eWxlIHR5cGU9InRleHQvY3NzIj4KCS5zdDB7ZmlsbDojMjMxRjIwO30KPC9zdHlsZT4KPGc+Cgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMC4wOSw3LjE1Yy0wLjAzLDAuMTktMC42Niw0Ljc0LDEuODksNy4yOWMxLjcsMS43MSw0LjMsMi4wMSw1Ljg5LDIuMDFjMC4zNywwLDAuNjgtMC4wMiwwLjkyLTAuMDMKCQljLTAuNDctMC4xNS0xLjM1LTAuNDYtMi4zLTAuOTlDNS43MSwxNSw1LjAyLDE0LjUsNC40NSwxMy45NmMtMC43Mi0wLjY5LTEuMjYtMS40NS0xLjYtMi4yN0MyLjQ5LDEwLjgxLDIuMzQsOS44MiwyLjQsOC43NQoJCWMwLjA0LTAuNzksMC4yLTEuNjMsMC40OC0yLjVjLTAuMi0wLjAxLTAuMzktMC4wMS0wLjU3LTAuMDFjLTAuNzksMC0xLjMzLDAuMDctMS4zOSwwLjA4bC0wLjcyLDAuMUwwLjA5LDcuMTV6Ii8+Cgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNC42OSwzLjQzTDQuMzIsNC4wNUM0LjI4LDQuMTIsMy44Nyw0LjgyLDMuNDksNS44NGMwLjg3LDAuMDgsMS42NywwLjI1LDIuNCwwLjQ5CgkJQzYuMjMsNS42NSw2LjY4LDQuOTYsNy4yNCw0LjNDNi4yNiwzLjg0LDUuNDgsMy42Myw1LjQsMy42MUw0LjY5LDMuNDN6Ii8+Cgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMy4yNywxMS41MWMxLjEzLDIuNzUsNC4zNCw0LjA0LDUuNTQsNC40M2MtMC4xMi0wLjEtMC4yOC0wLjI0LTAuNDYtMC40Yy0wLjEzLTAuMTItMC4yOC0wLjI3LTAuNDMtMC40MgoJCWMtMC4yNy0wLjI3LTAuNTYtMC41OS0wLjg1LTAuOTZjLTAuNTYtMC43LTEuMDEtMS40Mi0xLjMzLTIuMTRjLTAuNC0wLjkxLTAuNjEtMS44Mi0wLjYtMi43MWMwLTAuNywwLjEzLTEuNDIsMC4zOS0yLjEzCgkJYzAuMDMtMC4wNywwLjA1LTAuMTQsMC4wOC0wLjIxQzUuNjQsNi44OSw1LjY3LDYuODIsNS43LDYuNzVDNS4wNSw2LjU0LDQuNCw2LjQxLDMuNzksNi4zM0MzLjcyLDYuMzIsMy42NCw2LjMxLDMuNTcsNi4zMQoJCUMzLjQ5LDYuMywzLjQyLDYuMjksMy4zNCw2LjI4QzMuMzIsNi4zNiwzLjMsNi40MywzLjI3LDYuNUMzLjI1LDYuNTcsMy4yMyw2LjY1LDMuMjEsNi43MkMyLjgxLDguMTMsMi42MSw5Ljg5LDMuMjcsMTEuNTF6Ii8+Cgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMTAuNTksMi44MmwtMC41OC0wLjQ0TDkuNDIsMi44MkM5LjM2LDIuODcsOC43MiwzLjM1LDcuOTgsNC4xNUM4Ljc0LDQuNTYsOS40Miw1LjAyLDEwLDUuNTMKCQljMC41OC0wLjUxLDEuMjYtMC45NiwyLjAzLTEuMzZDMTEuMjksMy4zNiwxMC42NSwyLjg3LDEwLjU5LDIuODJ6Ii8+Cgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNi41Myw2LjU5QzYuNiw2LjYyLDYuNjcsNi42NSw2Ljc0LDYuNjhDNy4zMiw2Ljk2LDcuODQsNy4zLDguMjksNy43MWMwLjI2LTAuNTQsMC42MS0xLjA2LDEuMDUtMS41NAoJCUM5LjM5LDYuMTEsOS40NSw2LjA1LDkuNSw2QzkuNTUsNS45NCw5LjYsNS44OSw5LjY2LDUuODRDOS4xNSw1LjM5LDguNiw1LjAyLDguMDcsNC43MkM4LDQuNjgsNy45Myw0LjY1LDcuODcsNC42MQoJCUM3LjgsNC41Nyw3LjczLDQuNTQsNy42Niw0LjVjLTAuMDUsMC4wNi0wLjEsMC4xMi0wLjE1LDAuMTdDNy40Nyw0Ljc0LDcuNDIsNC44LDcuMzgsNC44NkM3LDUuMzQsNi42Miw1Ljg5LDYuMzIsNi41CgkJQzYuMzksNi41Myw2LjQ2LDYuNTUsNi41Myw2LjU5eiIvPgoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTYuMDQsNy4xMkM2LjAxLDcuMTksNS45OSw3LjI2LDUuOTYsNy4zM2MwLDAsMCwwLDAsMEM1Ljc0LDcuOTUsNS42LDguNjIsNS42LDkuMzEKCQljLTAuMDEsMi45NiwyLjQ0LDUuMzYsMy40MSw2LjJjLTAuMDctMC4xNS0wLjE3LTAuMzMtMC4yNy0wLjU1Yy0wLjA4LTAuMTctMC4xNi0wLjM1LTAuMjQtMC41NmMtMC4xMi0wLjMxLTAuMjUtMC42Ni0wLjM2LTEuMDQKCQljLTAuMjUtMC44MS0wLjQtMS42MS0wLjQ0LTIuMzZjLTAuMDUtMC44NSwwLjAzLTEuNjQsMC4yNC0yLjM3YzAuMDItMC4wOCwwLjA1LTAuMTcsMC4wOC0wLjI1YzAsMCwwLTAuMDEsMC0wLjAxCgkJQzguMDQsOC4zLDguMDcsOC4yMiw4LjEsOC4xNWMtMC40Ny0wLjQ1LTEtMC43OS0xLjU2LTEuMDZjMCwwLDAsMCwwLDBDNi40Nyw3LjA2LDYuNDEsNy4wMyw2LjM0LDdjMCwwLDAsMCwwLDAKCQlDNi4yNyw2Ljk3LDYuMiw2Ljk0LDYuMTMsNi45MUM2LjEsNi45OCw2LjA3LDcuMDUsNi4wNCw3LjEyQzYuMDQsNy4xMiw2LjA0LDcuMTIsNi4wNCw3LjEyeiIvPgoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTE1LjMxLDMuNDVMMTQuNiwzLjYzYy0wLjA4LDAuMDItMC44NSwwLjIyLTEuODQsMC42OGMwLjU1LDAuNjYsMC45OSwxLjMzLDEuMzMsMi4wMQoJCWMwLjE3LTAuMDYsMC4zNC0wLjExLDAuNTEtMC4xNmMwLjU5LTAuMTYsMS4yMi0wLjI3LDEuODgtMC4zM2MtMC4zNy0xLjAxLTAuNzYtMS42OS0wLjgtMS43NkwxNS4zMSwzLjQ1eiIvPgoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTEzLjQ1LDYuNTdjMC4wNy0wLjAzLDAuMTQtMC4wNiwwLjIxLTAuMDljLTAuMy0wLjYtMC42Ni0xLjE0LTEuMDMtMS42MWMtMC4wNS0wLjA2LTAuMDktMC4xMi0wLjE0LTAuMTgKCQljLTAuMDUtMC4wNi0wLjEtMC4xMi0wLjE1LTAuMThjLTAuMDcsMC4wMy0wLjEzLDAuMDctMC4yLDAuMTFjLTAuMDcsMC4wNC0wLjEzLDAuMDctMC4yLDAuMTFjLTAuNTMsMC4zLTEuMDksMC42Ny0xLjYxLDEuMTEKCQljMC4wNSwwLjA1LDAuMTEsMC4xMSwwLjE2LDAuMTZjMC4wNSwwLjA2LDAuMSwwLjExLDAuMTUsMC4xN2MwLjQyLDAuNDcsMC43NywwLjk4LDEuMDMsMS41MWMwLjQ1LTAuNCwwLjk4LTAuNzQsMS41Ny0xLjAyCgkJQzEzLjMxLDYuNjMsMTMuMzgsNi42LDEzLjQ1LDYuNTd6Ii8+Cgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMTAuMzEsNi40OEMxMC4zMSw2LjQ4LDEwLjMxLDYuNDgsMTAuMzEsNi40OGMtMC4wNS0wLjA2LTAuMS0wLjExLTAuMTYtMC4xN2MwLDAsMCwwLDAsMAoJCUMxMC4xLDYuMjUsMTAuMDUsNi4yLDkuOTksNi4xNUM5Ljk0LDYuMiw5Ljg5LDYuMjUsOS44Myw2LjMxYzAsMCwwLDAsMCwwYy0wLjA1LDAuMDUtMC4xLDAuMTEtMC4xNiwwLjE3YzAsMCwwLDAsMCwwCgkJQzkuMjYsNi45Myw4LjksNy40NSw4LjY0LDguMDRDOC42Myw4LjA2LDguNjIsOC4wOCw4LjYxLDguMDljMC4wNywwLjAzLDAuMTMsMC4wNywwLjE4LDAuMTNsMCwwYzAsMCwwLDAsMCwwCgkJYzAsMCwwLjAxLDAuMDEsMC4wMSwwLjAxYzAuMDYsMC4wNiwwLjExLDAuMTMsMC4xNiwwLjJjMC40LDAuNTEsMC43MywxLjA5LDAuOTksMS43NWMwLjI3LTAuNjcsMC42MS0xLjI3LDEuMDMtMS43OQoJCWMwLjA1LTAuMDcsMC4xMS0wLjEzLDAuMTctMC4yYzAuMDYtMC4wNiwwLjExLTAuMTIsMC4xNy0wLjE4QzExLjA2LDcuNDMsMTAuNzEsNi45MiwxMC4zMSw2LjQ4eiIvPgoJPHBhdGggY2xhc3M9InN0MCIgZD0iTTguNzMsOC44N0M4LjcsOC44Miw4LjY2LDguNzgsOC42Miw4LjczQzguNjEsOC43Miw4LjYsOC43LDguNiw4LjY5QzguNTUsOC42Myw4LjUsOC41OCw4LjQ1LDguNTIKCQlDOC40Myw4LjU5LDguNCw4LjY2LDguMzgsOC43M2MwLDAuMDEtMC4wMSwwLjAzLTAuMDEsMC4wNEM4LjM1LDguODQsOC4zNCw4LjksOC4zMiw4Ljk2QzguMzEsOC45OSw4LjMxLDkuMDIsOC4zLDkuMDUKCQljLTAuNTcsMi40NCwwLjQ5LDUsMS4wMiw2LjA3Yy0wLjAxLTAuMTctMC4wMy0wLjM4LTAuMDMtMC42MWMtMC4wMS0wLjE5LTAuMDEtMC4zOSwwLTAuNjFjMC4wMS0wLjM0LDAuMDItMC43MiwwLjA3LTEuMTIKCQljMC4wNC0wLjM0LDAuMDgtMC42NywwLjE1LTAuOThjMC4wMy0wLjE3LDAuMDctMC4zNCwwLjExLTAuNTFjMC4wNC0wLjE0LDAuMDctMC4yOCwwLjExLTAuNDJjLTAuMjEtMC42Ni0wLjUxLTEuMzItMC45My0xLjkxCgkJQzguNzcsOC45Myw4Ljc1LDguOSw4LjczLDguODd6Ii8+Cgk8cGF0aCBjbGFzcz0ic3QwIiBkPSJNMTcuMDksNi4yNmMtMC4wNywwLTAuMTUsMC4wMS0wLjIzLDAuMDFjLTAuMDgsMC0wLjE1LDAuMDEtMC4yMywwLjAyYy0wLjcyLDAuMDYtMS41NCwwLjE5LTIuMzUsMC40NgoJCWMtMC4wNywwLjAyLTAuMTQsMC4wNS0wLjIyLDAuMDdjLTAuMDcsMC4wMy0wLjE0LDAuMDUtMC4yMSwwLjA4Yy0wLjA3LDAuMDMtMC4xNCwwLjA2LTAuMjEsMC4wOWMtMC4wNywwLjAzLTAuMTQsMC4wNi0wLjIxLDAuMDkKCQljLTAuNTYsMC4yNi0xLjEsMC42LTEuNTcsMS4wNGMtMC4wNSwwLjA1LTAuMTEsMC4xLTAuMTYsMC4xNWMtMC4wMSwwLjAxLTAuMDEsMC4wMS0wLjAyLDAuMDJjLTAuMDYsMC4wNi0wLjEyLDAuMTItMC4xOCwwLjE5CgkJYy0wLjA2LDAuMDctMC4xMSwwLjEzLTAuMTcsMC4yYy0wLjA1LDAuMDYtMC4xLDAuMTMtMC4xNSwwLjE5Yy0wLjQ2LDAuNjItMC43NywxLjMtMC45OSwyYy0wLjA0LDAuMTQtMC4wOCwwLjI4LTAuMTIsMC40MgoJCWMtMC4wMywwLjEyLTAuMDYsMC4yMy0wLjA4LDAuMzVjLTAuMDEsMC4wNS0wLjAyLDAuMTEtMC4wMywwLjE2Yy0wLjAxLDAuMDMtMC4wMSwwLjA1LTAuMDIsMC4wOGMtMC4wMywwLjEzLTAuMDUsMC4yNi0wLjA3LDAuNAoJCWMtMC4wMSwwLjA4LTAuMDIsMC4xNi0wLjAzLDAuMjRDOS43LDEzLjUzLDkuNzMsMTQuNDQsOS43NywxNWMwLjAyLDAuMzIsMC4wNSwwLjUzLDAuMDYsMC41OGMwLDAsMCwwLDAsMGMwLDAsMCwwLDAsMC4wMQoJCWwwLjAyLDAuMTdsMC4wMiwwLjE0bDAuMDEsMC4xbDAuMDMsMC4xOGwwLjAxLDAuMDRsMCwwLjAybDAsMC4wMmwwLjAxLDAuMDVsMC4xMiwwLjAybDAuMSwwLjAxbDAuMDEsMGwwLjAyLDBsMC4wMSwwbDAuMTgsMC4wMwoJCWwwLjAxLDBsMC4wMSwwbDAuMDIsMGwwLDBsMC4yNCwwLjAzYzAuMDYsMC4wMSwwLjU5LDAuMDgsMS4zNiwwLjA4YzEuNTksMCw0LjIzLTAuMyw1Ljk0LTIuMDNjMi41NS0yLjU3LDEuOS03LjEzLDEuODgtNy4zMgoJCWwtMC4xMS0wLjcybC0wLjczLTAuMWMtMC4wNi0wLjAxLTAuNTktMC4wOC0xLjM2LTAuMDhDMTcuNDcsNi4yNCwxNy4yOSw2LjI1LDE3LjA5LDYuMjZ6IE0xNi41NywxMy4xCgkJYy0xLjIyLDEuMjQtMy4yNywxLjQ1LTQuNTQsMS40NWMtMC4xMiwwLTAuMjQsMC0wLjM0LTAuMDFjLTAuMDYtMS4zNiwwLjE0LTMuNjIsMS40LTQuODljMS4yMi0xLjI0LDMuMjctMS40NSw0LjU0LTEuNDUKCQljMC4xMiwwLDAuMjMsMCwwLjM0LDAuMDFDMTguMDIsOS40MSwxNy45MSwxMS43NCwxNi41NywxMy4xeiIvPgo8L2c+Cjwvc3ZnPgo=' );

	}

	/**
	 * Remove the main menu page as we will rely only on submenu pages
	 *
	 */
	public function remove_main_menu_page() {

		remove_submenu_page( 'dpsp-social-pug', 'dpsp-social-pug' );

	}

	/**
	 * Enqueue scripts and styles for the admin dashboard
	 *
	 * @param string $hook_suffix The current admin page. this is being run on
	 */
	public function init_admin_scripts( string $hook_suffix ) {

		if ( strpos( $hook_suffix, 'dpsp' ) !== false ) {
			wp_register_script( 'select2-js', DPSP_PLUGIN_DIR_URL . 'assets/libs/select2/select2.min.js', [ 'jquery' ] );
			wp_enqueue_script( 'select2-js' );
			wp_register_style( 'select2-css', DPSP_PLUGIN_DIR_URL . 'assets/libs/select2/select2.min.css' );
			wp_enqueue_style( 'select2-css' );

			wp_register_script(
				'dpsp-touch-punch-js',
				DPSP_PLUGIN_DIR_URL . 'assets/dist/jquery.ui.touch-punch.min.' . MV_GROW_VERSION . '.js',
				[
					'jquery-ui-sortable',
					'jquery',
				]
			);
			wp_enqueue_script( 'dpsp-touch-punch-js' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-picker' );
		}

		wp_register_style( 'dpsp-dashboard-style-pro', DPSP_PLUGIN_DIR_URL . 'assets/dist/style-dashboard-pro.' . self::$VERSION . '.css', [], self::$VERSION );
		wp_enqueue_style( 'dpsp-dashboard-style-pro' );

		wp_register_script(
			'dpsp-dashboard-js-pro',
			DPSP_PLUGIN_DIR_URL . 'assets/dist/dashboard-pro.' . self::$VERSION . '.js',
			[
				'jquery-ui-sortable',
				'jquery',
			],
			self::$VERSION
		);
		wp_enqueue_script( 'dpsp-dashboard-js-pro' );

		wp_register_style( 'dpsp-frontend-style-pro', DPSP_PLUGIN_DIR_URL . 'assets/dist/style-frontend-pro.' . self::$VERSION . '.css', [], self::$VERSION );
		wp_enqueue_style( 'dpsp-frontend-style-pro' );

	}

	/**
	 * Enqueue scripts that are Gutenberg specific
	 *
	 */
	public function init_gutenberg_scripts() {

		$screen = get_current_screen();
		// Don't load on the widgets screen because these scripts conflict with the widget editor scripts
		if ( $screen && 'widgets' === $screen->id ) {
			return false;
		}
		$IS_DEVELOPMENT = apply_filters( 'mv_grow_dev_mode', false );
		$script_url     = $IS_DEVELOPMENT ? DPSP_PLUGIN_DIR_URL . 'assets/dist/dev-entry.js' : DPSP_PLUGIN_DIR_URL . 'assets/dist/block-editor.' . self::$VERSION . '.js';
		wp_enqueue_script(
			'dpsp-block-editor',
			$script_url,
			[
				'wp-components',
				'wp-blocks',
				'wp-compose',
				'wp-editor',
				'wp-element',
				'wp-i18n',
			],
			self::$VERSION
		);

	}

	/**
	 * Fallback for setting defaults when updating the plugin,
	 * as register_activation_hook does not fire for automatic updates
	 *
	 */
	public function update_database() {

		$dpsp_db_version = Mediavine\Grow\Settings::get_setting( 'dpsp_version', '' );

		if ( self::$VERSION !== $dpsp_db_version ) {

			dpsp_default_settings();
			update_option( 'dpsp_version', self::$VERSION );

			// Add first time activation
			if ( '' === Mediavine\Grow\Settings::get_setting( 'dpsp_first_activation', '' ) ) {

				update_option( 'dpsp_first_activation', time() );

				/**
				 * Do extra actions on plugin's first ever activation
				 *
				 */
				do_action( 'dpsp_first_activation' );

			}

			// Update Sidebar button style from 1,2,3 to 1,5,8
			$dpsp_location_sidebar = dpsp_get_location_settings( 'sidebar' );

			if ( '2' === $dpsp_location_sidebar['button_style'] ) {
				$dpsp_location_sidebar['button_style'] = 5;
			}

			if ( '3' === $dpsp_location_sidebar['button_style'] ) {
				$dpsp_location_sidebar['button_style'] = 8;
			}

			update_option( 'dpsp_location_sidebar', $dpsp_location_sidebar );

			/**
			 * Do extra database updates on plugin update
			 *
			 * @param string $dpsp_db_version - the previous version of the plugin
			 * @param string DPSP_VERSION     - the new (current) version of the plugin
			 *
			 */
			do_action( 'dpsp_update_database', $dpsp_db_version, self::$VERSION );

		}

	}

	/**
	 * Add custom plugin CSS classes to the admin body classes
	 *
	 */
	public function admin_body_class( $classes ) {
		$page = filter_input( INPUT_GET, 'page' );
		if ( empty( $page ) ) {
			return $classes;
		}

		if ( false === strpos( $page, 'dpsp-' ) ) {
			return $classes;
		}

		return $classes . ' dpsp-pagestyles';

	}

	/**
	 * Add extra action links in the plugins page
	 *
	 */
	public function add_plugin_action_links( $links ) {

		$links[] = '<a href="' . esc_url( get_admin_url( null, 'admin.php?page=dpsp-toolkit' ) ) . '">' . __( 'Settings', 'social-pug' ) . '</a>';

		return $links;

	}

	/**
	 * Include plugin files for the admin area
	 */
	public function load_resources_admin() {
		$this->setup_integrations();
	}

	/**
	 * Whether or not this instance of the plugin is free
	 * @return bool
	 */
	public static function is_free() {
		return (bool) apply_filters( 'mv_grow_is_free', false );
	}

	/**
	 * Are pro-level features available?
	 *
	 * @return bool
	 */
	public function is_pro() : bool {
		return ! self::is_free();
	}

	/**
	 * Return the branding name based on free vs pro
	 *
	 * @return string
	 */
	public static function get_branding_name() {
		if ( Social_Pug::is_free() ) {
			return __( 'Grow Social by Mediavine', 'social-pug' );
		}

		return __( 'Grow Social Pro by Mediavine', 'social-pug' );
	}
}
