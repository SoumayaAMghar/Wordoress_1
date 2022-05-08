<?php
namespace Mediavine\Grow;

class Asset_Loader extends \Social_Pug {

	/** @var null|self Singleton instance of the class */
	private static $instance = null;

	/** @var string $script_handle WordPress handle for front end JS */
	public static $script_handle = 'dpsp-frontend-js-pro';

	/** @var string $style_handle WordPress handle for front end CSS */
	public static $style_handle = 'dpsp-frontend-style-pro';

	/**
	 * Get instance of Class.
	 *
	 * @return Asset_Loader
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Set up hooks.
	 */
	public function init() {
		add_filter( 'script_loader_tag', [ $this, 'add_async_attribute' ], 10, 2 );
		add_filter( 'style_loader_tag', [ $this, 'add_async_styles' ], 10, 3 );
		add_action( 'wp_head', [ $this, 'output_inline_styles' ] );
	}

	/**
	 * Add async attributes to Script tag.
	 *
	 * @param $tag
	 * @param $handle
	 * @return string|string[]
	 */
	public function add_async_attribute( $tag, $handle ) {
		if ( substr( $handle, 0, strlen( self::$script_handle ) ) === self::$script_handle ) {
			$tag = str_replace( ' src', ' async data-noptimize src', $tag );
		}

		return $tag;
	}

	/**
	 * Adds async when we aren't sure if styles are needed
	 *
	 * @param string $tag script tag to be outputted
	 * @param string $handle enqueue handle
	 * @param string $href Value for tag
	 * @return string script tag to be outputted
	 */
	public static function add_async_styles( $tag, $handle, $href ) {
		if ( self::will_style() ) {
			return $tag;
		}
		$prefix = self::$style_handle;
		if ( substr( $handle, 0, strlen( $prefix ) ) === $prefix ) {
			$tag = '<link rel="preload" class="mv-grow-style" href="' . $href . '" as="style">' . "<noscript>$tag</noscript>";
		}

		return $tag;
	}

	/**
	 * Register the Scripts and Styles that will run in the front end.
	 */
	public static function register_front_end_scripts() {
		// Development mode activated via filter
		$IS_DEVELOPMENT = apply_filters( 'mv_grow_dev_mode', false );

		$settings = Settings::get_setting( 'dpsp_settings' );

		$is_free = \Social_Pug::is_free();

		$script_filename = $is_free ? 'front-end-free' : 'front-end-pro';

		if ( $IS_DEVELOPMENT ) {
			if ( isset( $settings['legacy_javascript'] ) && $settings['legacy_javascript'] ) {
				wp_register_style( self::$style_handle, DPSP_PLUGIN_DIR_URL . 'assets/dist/dev-entry-jquery.css', [], self::$VERSION );
				wp_register_script( self::$script_handle, DPSP_PLUGIN_DIR_URL . 'assets/dist/dev-entry-jquery.js', [ 'jquery' ], self::$VERSION, true );
			} else {
				wp_register_style( self::$style_handle, DPSP_PLUGIN_DIR_URL . 'assets/dist/dev-entry.css', [], self::$VERSION );
				wp_register_script( self::$script_handle, DPSP_PLUGIN_DIR_URL . 'assets/dist/dev-entry.js', [], self::$VERSION, true );
			}
		} else {
			if ( isset( $settings['legacy_javascript'] ) && $settings['legacy_javascript'] && ! $is_free ) {
				wp_register_style( self::$style_handle, DPSP_PLUGIN_DIR_URL . 'assets/dist/style-frontend-pro-jquery.' . self::$VERSION . '.css', [], self::$VERSION );
				wp_register_script( self::$script_handle, DPSP_PLUGIN_DIR_URL . 'assets/dist/front-end-pro-jquery.' . self::$VERSION . '.js', [ 'jquery' ], self::$VERSION, true );
			} else {
				wp_register_style( self::$style_handle, DPSP_PLUGIN_DIR_URL . 'assets/dist/style-frontend-pro.' . self::$VERSION . '.css', [], self::$VERSION );
				wp_register_script( self::$script_handle, DPSP_PLUGIN_DIR_URL . 'assets/dist/' . $script_filename . '.' . self::$VERSION . '.js', [], self::$VERSION, true );
			}
		}
	}

	/**
	 * Enqueue Style and Script.
	 */
	public static function enqueue_scripts() {
		do_action( 'dpsp_pre_enqueue_frontend_scripts' );
		wp_enqueue_style( self::$style_handle );
		wp_enqueue_script( self::$script_handle );
		do_action( 'dpsp_post_enqueue_frontend_scripts' );
	}

	/**
	 * Determine if Styles are definitely needed, or defer that decision later on.
	 *
	 * @return bool
	 */
	public static function will_style() {
		$should_load = false;
		// Always load if on admin
		if ( is_admin() ) {
			$should_load = true;
		}
		$active_tools      = dpsp_get_active_tools();
		$single_only_tools = [
			'share_content',
			'share_sidebar',
			'share_images',
			'share_sticky_bar',
			'share_pop_up',
		];
		$widget_tools      = [ 'follow_widget' ];
		if ( is_singular() ) {
			$should_load = $should_load ? true : count( array_intersect( $single_only_tools, $active_tools ) ) > 0;
		}
		foreach ( $widget_tools as $tool ) {
			if ( in_array( $tool, $active_tools, true ) ) {
				$should_load = true;
			}
		}

		return apply_filters( 'mv_grow_styles_should_enqueue', $should_load );
	}

	/**
	 * Dequeue Scripts if not needed.
	 */
	public static function maybe_dequeue() {
		$should_load       = false;
		$active_tools      = dpsp_get_active_tools();
		$single_only_tools = [
			'share_content',
			'share_sidebar',
			'share_images',
			'share_sticky_bar',
			'share_pop_up',
		];
		if ( is_singular() ) {
			$should_load = $should_load ? true : count( array_intersect( $single_only_tools, $active_tools ) ) > 0;
		}
		$should_load = apply_filters( 'mv_grow_scripts_should_enqueue', $should_load );
		if ( ! $should_load ) {
			wp_dequeue_script( self::$script_handle );
		}
	}

	/**
	 * Echo accumulated inline styles into the <head> of the document, this function called by wp_head hook.
	 */
	public function output_inline_styles() {
		echo '<style type="text/css" data-source="Grow Social by Mediavine">' . esc_attr( apply_filters( 'dpsp_output_inline_style', '' ) ) . '</style>';
	}
}
