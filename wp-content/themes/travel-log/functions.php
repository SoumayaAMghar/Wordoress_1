<?php
/**
 * Travel Log functions and definitions
 *
 * @package Travel_Log
 */

if ( ! function_exists( 'travel_log_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function travel_log_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Travel Log, use a find and replace
		 * to change 'travel-log' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'travel-log', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

			/*
			 * Enable support for Custom Logo.
			 *
			 * @link https://developer.wordpress.org/themes/functionality/custom-logo/
			 */
			$defaults = array(
				'height'      => 60,
				'width'       => 150,
				'flex-height' => true,
				'flex-width'  => true,
				'header-text' => array( 'site-title', 'site-description' ),
				);
				add_theme_support( 'custom-logo', $defaults );

				// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'top-left-links' => esc_html__( 'Top Left Links', 'travel-log' ),
			'social-links' => esc_html__( 'Social Links', 'travel-log' ),
			'primary-menu' => esc_html__( 'Primary', 'travel-log' ),
			'footer-menu' => esc_html__( 'Footer', 'travel-log' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'travel_log_custom_background_args', array(
			'default-color' => 'f9f9f9',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );
		
		add_theme_support( 'align-wide' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_editor_style( 'css/gutenberg/gutenberg-editor.css' );
	}
endif;
add_action( 'after_setup_theme', 'travel_log_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function travel_log_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'travel_log_content_width', 1170 );
}
add_action( 'after_setup_theme', 'travel_log_content_width', 0 );

/**
*  Loader.
*/
require get_template_directory() . '/inc/load.php';

if ( ! function_exists( 'travel_log_wp_travel_support_get_post_type' ) ) {

	/**
	 * WP Travel Post Type
	 *
	 * @return string $post_type [ WP_TRAVEL_POST_TYPE ]
	 */
	function travel_log_wp_travel_support_get_post_type(){

		if ( defined( 'WP_TRAVEL_POST_TYPE' ) ) {

			return $post_type = WP_TRAVEL_POST_TYPE;

		}

		return $post_type = 'itineraries';

	}

}

/**
 * Check if block is enabled in singular.
 *
 * @return boolean
 */
function travel_log_is_singular_block_enabled() {
	if ( ! is_singular() ) {
		return false;
	}
	global $post;
	return ( function_exists( 'has_blocks' ) && has_blocks( $post->ID ) );
}

include 'inc/block-functions.php';
