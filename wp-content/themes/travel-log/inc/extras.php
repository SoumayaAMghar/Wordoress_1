<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Travel_Log
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function travel_log_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	return $classes;
}
add_filter( 'body_class', 'travel_log_body_classes' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function travel_log_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'travel_log_pingback_header' );

if ( ! function_exists( 'travel_log_primary_menu_fallback' ) ) :

	/**
	 * Fallback for Primary menu.
	 * @since 1.0.0
	 */
	function travel_log_primary_menu_fallback( $args ) {

		echo '<ul>';
		echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'travel-log' ) . '</a></li>';
		wp_list_pages( array(
			'title_li' => '',
			'depth'    => 1,
			'number'   => 8,
		) );
		echo '</ul>';

	}
endif;


if ( ! function_exists( 'travel_log_social_share_meta' ) ) :

	/**
	 * [travel_log_social_share_meta Diusplay the Social share Blog Meta]
	 *
	 * @since 1.0.0
	 */
	function travel_log_social_share_meta() {

		$social_share_disable = travel_log_get_theme_option( 'travel_log_social_share_disable' );

		$fb_enable = travel_log_get_theme_option( 'sharer_fb_enable' );

		$twitter_enable = travel_log_get_theme_option( 'sharer_twitter_enable' );

		$gplus_enable = travel_log_get_theme_option( 'sharer_gplus_enable' );

		if ( true === $social_share_disable ) :

			return;

			endif;

		if ( true == $fb_enable || true === $twitter_enable || true === $gplus_enable ) :

	?>

	<div class="share-handle card-share">
	   <div class="social-reveal">
	   	<?php if ( true === $fb_enable ) : ?>
		   <!--Facebook-->
		   <a type="button" title="Share on Facebook" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" class="btn-floating btn-fb "><i class="wt-icon-brands wt-icon-facebook-f"></i></a>
		<?php endif; ?>
		<?php if ( true === $twitter_enable ) : ?>
		   <!--Twitter-->
		   <a type="button" title="Share on Twitter" target="_blank" href="https://twitter.com/share?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>" class="btn-floating btn-tw "><i class="wt-icon-brands wt-icon-twitter"></i></a>
		<?php endif; ?>
		<?php if ( true === $gplus_enable ) : ?>
		   <!--Google -->
		   <a type="button" title="Share on Google Plus" target="_blank" href="https://plus.google.com/share?url=<?php the_permalink(); ?>" class="btn-floating btn-gplus "><i class="wt-icon-brands wt-icon-google-plus-g"></i></a>
			<?php endif; ?>
	   </div>
	   <a class="btn-floating share-toggle float-right"><i class="wt-icon wt-icon-share-alt"></i></a>
	</div>

<?php

endif;

	}

	add_action( 'travel_log_social_share', 'travel_log_social_share_meta' );

endif;

if ( ! function_exists( 'travel_log_active_footer_count' ) ) :
	/**
	 * [travel_log_active_footer_count count active footers]
	 *
	 * @return [INT] [Number of active footer widgets]
	 *
	 * @since 1.0.0
	 */
	function travel_log_active_footer_count() {

		$footer_areas = array( 'footer-sidebar', 'footer-sidebar-2', 'footer-sidebar-3', 'footer-sidebar-4' );

		$count = 0;

		foreach ( $footer_areas as $footer ) {

			if ( is_active_sidebar( $footer ) ) :

				$count++;

			endif;

		}

		return $count;

	}

endif;

if ( ! function_exists( 'travel_log_sidebar_position' ) ) :
	/**
	 * Sidebar position
	 *
	 * @return array Sidbar positions
	 */
	function travel_log_sidebar_position() {
		$travel_log_sidebar_position = array(
	  	'left' => esc_html( 'Left Sidebar', 'travel-log' ),
		'right' => esc_html__( 'Right Sidebar', 'travel-log' ),
		'full'    => esc_html__( 'No Sidebar', 'travel-log' ),
		);

		$output = apply_filters( 'travel_log_sidebar_position', $travel_log_sidebar_position );

		return $output;
	}
endif;


if ( ! function_exists( 'travel_slider_additional_button' ) ) :
	/**
	 * Slider additional button
	 *
	 * @return string
	 */
	function travel_slider_additional_button() {

		if ( ! class_exists( 'WP_Travel' ) ) :

			return;

		endif;

		ob_start();

		if ( true === travel_log_get_theme_option( 'home_slider_book_now_enable' ) && 'category' !== travel_log_get_theme_option( 'slider_content_type' ) ) :

			$book_now_text = apply_filters( 'travel_log_slider_book_now_text', __( 'Book Now', 'travel-log' ) );

		?>
			<button onclick="window.location.href='<?php echo esc_url( get_the_permalink() ) . '#booking'; ?>';"   class="slider-book-now"><?php echo esc_html( $book_now_text ); ?></button>

		<?php
		endif;

		$output = ob_get_clean();

		return $output;
	}
endif;

