<?php
/**
 * Theme assets function file.
 *
 * @package Travel_Log
 */

/**
 * Enqueue scripts and styles.
 *
 * @since  1.0.0 [Enqueue theme scripts and styles.]
 */
function travel_log_scripts() {
	// if ( travel_log_is_singular_block_enabled() ) {
	// 	return false;
	// }
	$defaults = travel_log_default_values();

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_register_style( 'travel-log-style', get_stylesheet_uri() );

	if ( is_admin_bar_showing() ) :

		$custom_css = '@media only screen and (min-width: 600px) and (max-width: 767px){ #sidr-main{top: 45px;}}@media only screen and (min-width: 767px){ #sidr-main{top: 32px;}}';

		wp_add_inline_style( 'travel-log-style', $custom_css );

	endif;

	$post_id = get_queried_object_id();
	$featured_as_banner = get_post_meta( $post_id, 'travel-log-header-image-meta', true );
	if ( 'yes' == $featured_as_banner ) :
		$banner_image = get_the_post_thumbnail_url( $post = $post_id, $size = 'full' );
	else :
		if ( is_tax( array( 'itinerary_types', 'travel_locations', 'travel_keywords', 'activity' ) ) ) {
			$term_id = get_queried_object()->term_id;
			$banner_image = get_term_meta( $term_id, 'wp_travel_trip_type_image_id', true );
			$banner_image = wp_get_attachment_image_url( $banner_image, 'full' );
		} else{
			$banner_image = travel_log_get_theme_option( 'header_banner_image' );
		}
	endif;

	if ( ! empty( $banner_image ) ) :

		$custom_css = '.custom-header{ background-image: url(' . esc_url( $banner_image ) . '); }';

		wp_add_inline_style( 'travel-log-style', $custom_css );

	endif;

	wp_enqueue_style( 'travel-log-style' );

	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/css/all.min.css', array(), '4.7.0' );

	wp_enqueue_style( 'slick', get_template_directory_uri() . '/css/slick.min.css', array(), '1.3.15' );

	wp_enqueue_style( 'travel-log-sidr', get_template_directory_uri() . '/css/jquery.sidr.dark.min.css' );

	wp_enqueue_style( 'jquery-animate-css',  get_template_directory_uri() . '/css/animate.min.css' ,array( 'font-awesome' ), '1.0.0' );

	wp_enqueue_style( 'travel-log-roboto-font-css', 'https://fonts.googleapis.com/css?family=Roboto:400,500,700' );

	wp_enqueue_style( 'travel-log-front-style', get_template_directory_uri() . '/css/front-style' . $suffix . '.css', array( 'font-awesome' ), '1.0.0' );

	wp_enqueue_style( 'jquery-fancy-box-css', get_template_directory_uri() . '/css/jquery.fancybox.min.css' );

	wp_enqueue_script( 'jquery-fancy-box-js', get_template_directory_uri() . '/js/jquery.fancybox.min.js', array( 'jquery' ), '', true );

	wp_register_script( 'jquery-slick', get_template_directory_uri() . '/js/slick.min.js', array( 'jquery' ), '1.3.15', true );

	wp_enqueue_script( 'jquery-sidr', get_template_directory_uri() . '/js/jquery.sidr.min.js', array( 'jquery' ), '1.2.1', true );

	wp_enqueue_script( 'jquery-isotope-pkgd-js', get_template_directory_uri() . '/js/isotope-pkgd.min.js', array( 'jquery' ), '', true );

	wp_enqueue_script( 'travel-log-script', get_template_directory_uri() . '/js/travel-log.js', array( 'jquery-slick', 'jquery-sidr' ), '1.0.0', true );

	wp_enqueue_script( 'travel-log-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( 'travel-log-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Localize the script with new data.
	$translation_array = array(
		'home_slider_speed' => travel_log_get_theme_option( 'home_slider_speed' ),
		'home_testimonial_speed' => travel_log_get_theme_option( 'testimonials_speed' ),
		'home_latest_posts_speed' => travel_log_get_theme_option( 'latest_posts_speed' ),
		'loader_disable' => travel_log_get_theme_option( 'travel_log_loader' ),
		'locale_dir' => is_rtl() ? true : false,
		'sidr_side' => is_rtl() ? 'left' : 'right',
	);

	wp_localize_script( 'travel-log-script', 'travel_log', $translation_array );

	// Enqueued script with localized data.
	wp_enqueue_script( 'travel-log-script' );

	if ( travel_log_is_singular_block_enabled() ) {
		wp_enqueue_style( 'travel-log-gutenberg-style', get_template_directory_uri() . '/css/gutenberg/gutenberg-style.css', array( 'travel-log-style' ), '1.0.0' );
	}
}
add_action( 'wp_enqueue_scripts', 'travel_log_scripts' );
