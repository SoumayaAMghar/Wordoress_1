<?php
/**
 * Template functions.
 *
 * @package Travel_Log
 */

/* General functions. */

if ( ! function_exists( 'travel_log_has_front_page' ) ) :

	/**
	 * Check if static front page is set.
	 *
	 * @return boolean
	 *
	 * @since 1.0.0
	 */
	function travel_log_has_front_page() {
		return 'page' === get_option( 'show_on_front' );
	}

endif;

if ( ! function_exists( 'travel_log_trip_content' ) ) :

	/**
	 * [travel_log_trip_content description]
	 *
	 * @param  array $args [description].
	 *
	 * @return mixed [HTML]
	 *
	 * @since 1.0.0
	 */
	function travel_log_trip_content( $args = array() ) {
		?>
		<div class="post-item-wrapper">
		<a href="<?php the_permalink(); ?>">
			<div class="post-thumb">

				<?php

				if ( has_post_thumbnail() ) :

					the_post_thumbnail( apply_filters( 'travel_log_trip_content_thumbnail_size', $args['thumbnail_size'] ) );

				else :

					travel_log_no_slider_thumbnail();

				endif;
				?>

			</div>
			<span class="effect"></span>
			<div class="post-content">
				<h4 class="post-title"><?php the_title(); ?></h4>
				<div class="read-more-link"><?php esc_html_e( 'Read More', 'travel-log' ); ?></div>
			</div>
		</a>
		</div>
	<?php
	}

endif;

if ( ! function_exists( 'travel_log_post_content' ) ) :
	/**
	 * [travel_log_post_content Post Contents]
	 *
	 * @param  array $args [description].
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	function travel_log_post_content( $args = array() ) {
		?>
		<div class="blog-latest-post">
			<div class=" post-item-wrapper">
				<div class="post-thumb">
					<a href="<?php the_permalink(); ?>">
						<?php
						if ( has_post_thumbnail() ) :
							the_post_thumbnail( apply_filters( 'travel_log_post_thumbnail_size', $args['thumbnail_size'] ) );
						else :
							travel_log_no_slider_thumbnail();
						endif;
						?>
						<span class="effect"></span>
					</a>
				</div>
				<div class="post-content">
				<h4 class="post-title"><a href="<?php the_permalink(); ?>" class="text-title"><?php the_title(); ?></a></h4>
						<span class="posted-on">
							<time class="entry-date published" datetime=""><?php echo get_the_date(); ?></time>
						</span>
						<div class="post-excerpt">
							<?php the_excerpt(); ?>
						</div>
				<div class="post-metas">
						<div class="meta-left">
							<a href="<?php the_permalink(); ?>" class="read-more-link"><?php esc_html_e( 'Read More', 'travel-log' ); ?></a>
						</div>
						<div class="meta-right">
							<span class="comments-links">
								<span class="screen-reader-text"></span>
								<a class="comments-number" href="<?php comments_link(); ?>"><?php comments_number( '0', '1', '%' ); ?></a>
							</span>

							<?php

								/** Social Share.
								 *
								 * @Hook : travel_log_social_share
								 *
								 * @hooked : travel_log_social_share_meta-10
								 */
								do_action( 'travel_log_social_share' );
							?>

						</div>
				</div>
				</div>
			</div>
		</div>
	<?php
	}

endif;

if ( ! function_exists( 'travel_log_no_slider_thumbnail' ) ) :

	/**
	 * Image to show if slider image is empty.
	 *
	 * @param  boolean $return Return or echo.
	 *
	 * @return string          Image output.
	 *
	 * @since 1.0.0
	 */
	function travel_log_no_slider_thumbnail( $return = false ) {
		$image_url = get_template_directory_uri() . '/images/blank_slider.png';
		$img = '<img src="' . $image_url . '" width="1300" height="500" alt="blank_slider" />';
		if ( $return ) {
			return $img;
		}

		echo wp_kses_post( $img );
	}

endif;


/* Header Templates Starts */

if ( ! function_exists( 'travel_log_customize_partial_blogname' ) ) :
	/**
	 * [travel_log_customize_partial_blogname Blog Name]
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	function travel_log_customize_partial_blogname() {
		bloginfo( 'name' );
	}

endif;

if ( ! function_exists( 'travel_log_customize_partial_blogdescription' ) ) :

	/**
	 * [travel_log_customize_partial_blogdescription Blog Description]
	 *
	 * @return void
	 */
	function travel_log_customize_partial_blogdescription() {
		bloginfo( 'description' );
	}

endif;

/**
 * Add body class as per the layout setup.
 *
 * @param  array $classes Body classes.
 * @return array
 */
function travel_log_layout_class( $classes ) {

	if ( ! display_header_text() ) {
		$classes[] = 'title-tagline-hidden';
	}
	if ( ( is_front_page() && ! is_home() ) || is_404() || is_search() ) {

		$classes[] = 'layout-full-width';

		return $classes;
	}

	$defaults = travel_log_default_values();

	global $post;

	if ( ! $post ) {

		return;
	}

	$sidebar_meta = get_post_meta( $post->ID, 'travel-log-sidebar-position', true );

	if ( '' !== $sidebar_meta && 'default' !== $sidebar_meta ) :

		if ( 'full' === $sidebar_meta ) {
			$classes[] = 'layout-full-width';
		}
		if ( 'left' === $sidebar_meta ) {
			if ( is_active_sidebar( 'sidebar-1' ) ) {
				$classes[] = 'layout-left-sidebar';
			} else {
				$classes[] = 'layout-full-width';
			}
		}
		if ( 'right' === $sidebar_meta ) {
			if ( is_active_sidebar( 'sidebar-1' ) ) {
				$classes[] = 'layout-right-sidebar';
			} else {
				$classes[] = 'layout-full-width';
			}
		}
		return $classes;

	endif;

	$layout = travel_log_get_theme_option( 'travel_log_layout' );

	if ( is_archive( travel_log_wp_travel_support_get_post_type() ) ) {

		if ( 'full' === $layout ) {
			$classes[] = 'layout-full-width';
		} elseif ( is_active_sidebar( 'wp-travel-archive-sidebar' ) ) {

			$classes[] = 'layout-' . $layout . '-sidebar';

		} else {

			$classes[] = 'layout-full-width';

		}

		return $classes;

	}

	if ( 'full' === $layout ) {
		$classes[] = 'layout-full-width';
	} elseif ( 'left' === $layout ) {
		if ( is_active_sidebar( 'sidebar-1' ) ) {
			$classes[] = 'layout-left-sidebar';
		} else {
			$classes[] = 'layout-full-width';
		}
	} elseif ( 'right' === $layout ) {
		if ( is_active_sidebar( 'sidebar-1' ) ) {
			$classes[] = 'layout-right-sidebar';
		} else {
			$classes[] = 'layout-full-width';
		}
	} else {
		$classes[] = 'layout-full-width';
	}
	return $classes;
}
add_action( 'body_class', 'travel_log_layout_class' );

if ( ! function_exists( 'travel_log_excerpt_length' ) ) :

	/**
	 * Set the post excerpt length to 40 words.
	 *
	 * @param int $length The number of excerpt characters.
	 * @return int The filtered number of characters.
	 */
	function travel_log_excerpt_length( $length ) {
		return 40;
	}

endif;

add_filter( 'excerpt_length', 'travel_log_excerpt_length' );

if ( ! function_exists( 'travel_log_show_header' ) ) :
	/**
	 * Site header section.
	 *
	 * @since 1.0.0
	 */
	function travel_log_show_header() {
		$header_image       = get_custom_header();
		$header_image_style = '';
		$header_image_class = '';
		if ( '' !== $header_image->url ) {
			$header_image_style = 'background-image: url(' . $header_image->url . ');background-position: 50%;background-size: cover;';
			$header_image_class = 'site-header-image';
		}
		?>
		<header id="masthead" class="site-header <?php echo esc_attr( $header_image_class ); ?>" role="banner" style="<?php echo esc_attr( $header_image_style ); ?>">
	<div class="container">
		<?php
		do_action( 'travel_log_after_header_container_open' );
		?>
		<div class="header-main-menu">
			<div class="site-branding">
				<?php
				/**
				 * Hook travel_log_site_branding.
				 *
				 * @hooked travel_log_site_identity - 10
				 */
				do_action( 'travel_log_site_branding' );
				?>
				</div><!-- .site-branding -->

				<div id="main-nav" class="">
				<?php
				/**
				 * Hook travel_log_main_nav.
				 *
				 * @hooked travel_log_main_menu - 10
				 */
				do_action( 'travel_log_main_nav' );
				?>
				</div>
				<div id="header-search">
				<a href="#search-form">
					<i class="wt-icon wt-icon-search"></i>
				</a>
				<div id="search-form">
					<span class="close"><i class="wt-icon wt-icon-times"></i></span>
						<?php
						$header_itinerary_search = travel_log_get_theme_option( 'travel_log_enable_header_wp_travel_search' );

						if ( class_exists( 'WP_Travel' ) && true === $header_itinerary_search ) { ?>
							<section id="section-itinerary-search" class="section-itinerary-search">
								<div class="container">
									<div class="row">
										<div class="col-sm-12">
											<?php travel_log_itinerary_search_form(); ?>
										</div>
									</div>
								</div>
							</section>
						<?php
						} else {
							// Get Default Search Form.
							get_search_form();
						}
						?>
				</div>
				</div>

				</div>
				<?php
				/**
				 * Hook travel_log_before_header_container_close.
				 */
				do_action( 'travel_log_before_header_container_close' );

				?>
			</div>
		</header><!-- #masthead -->
		<?php
	}

endif;

add_action( 'travel_log_header', 'travel_log_show_header' );

if ( ! function_exists( 'travel_log_top_header' ) ) :

	/**
	 * Top header tags before header.
	 *
	 * @since 1.0.0
	 */
	function travel_log_top_header() {
		?>
		<div class="top-header">
		<div class="container">
			<div class="row">
				<div class="col-sm-6">
						<?php
						/**
						 * Hook travel_log_top_header_left.
						 *
						 * @hooked travel_log_top_header_left_content - 10
						 */
						do_action( 'travel_log_top_header_left' );
						?>
						</div>
						<div class="col-sm-6">
						<?php
						/**
						 * Hook travel_log_top_header_right.
						 *
						 * @hooked travel_log_top_header_right_content - 10
						 */
						do_action( 'travel_log_top_header_right' );
						?>
						</div>
					</div>
				</div>
			</div>
			<?php
	}

endif;

add_action( 'before_header', 'travel_log_top_header' );

if ( ! function_exists( 'travel_log_mobile_nav' ) ) :

	/**
	 * [travel_log_mobile_nav Mobile Nav]
	 *
	 * @return [type] [description]
	 *
	 * @since 1.0.0
	 */
	function travel_log_mobile_nav() {
		?>
		<a id="simple-menu" href="#sidr-main" class="travel-mobile-menu"><i class="wt-icon wt-icon-bars"></i></a>
		<div id="mobile-nav"  class="sidr right">
		<?php wp_nav_menu( array(
			'theme_location' => 'primary-menu',
			'menu_id' => 'mobile-sidr-menu',
			'menu_class' => 'menu-mobile',
			'container' => false,
			'menu' => 'mainMenu',
			'fallback_cb'    => 'travel_log_primary_menu_fallback',
		) );
		?>
		</div>
		<?php
	}

endif;

add_action( 'travel_log_after_header_container_open', 'travel_log_mobile_nav' );

if ( ! function_exists( 'travel_log_top_header_left_content' ) ) :

	/**
	 * Top header left content.
	 *
	 * @since 1.0.0
	 */
	function travel_log_top_header_left_content() {
		wp_nav_menu( array( 'theme_location' => 'top-left-links', 'container_class' => 'header-info menu-icons', 'fallback_cb' => false ) );
	}

endif;

add_action( 'travel_log_top_header_left', 'travel_log_top_header_left_content' );

if ( ! function_exists( 'travel_log_top_header_right_content' ) ) :

	/**
	 * Top header right content.
	 *
	 * @since 1.0.0
	 */
	function travel_log_top_header_right_content() {
		wp_nav_menu( array( 'theme_location' => 'social-links', 'container_class' => 'header-social menu-icons', 'fallback_cb' => false ) );
	}

endif;

add_action( 'travel_log_top_header_right', 'travel_log_top_header_right_content' );

if ( ! function_exists( 'travel_log_site_identity' ) ) :

	/**
	 * Show site logo or title in header.
	 *
	 * @since 1.0.0
	 */
	function travel_log_site_identity() {
		?>
		<div id="site-identity">
			<?php

			if ( has_custom_logo() ) {
				?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home" itemprop="url">
					<?php the_custom_logo(); ?>
				</a>
			<?php
			} ?>
			<div class="site-branding-text">
				<?php if ( is_front_page() ) : ?>
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php else : ?>
					<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php endif; ?>
				<?php
				$description = get_bloginfo( 'description', 'display' );
				if ( $description || is_customize_preview() ) : ?>
					<p class="site-description"><?php echo $description; /* WPCS: xss ok. */ ?></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

endif;

add_action( 'travel_log_site_branding', 'travel_log_site_identity' );

if ( ! function_exists( 'travel_log_main_menu' ) ) :

	/**
	 * Show Main menu in header.
	 *
	 * @since 1.0.0
	 */
	function travel_log_main_menu() {
		?>
		<nav id="site-navigation" class="main-navigation" role="navigation">
		<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'travel-log' ); ?></button>
		<div class="wrap-menu-content">
			<?php wp_nav_menu( array( 'theme_location' => 'primary-menu', 'menu_id' => 'primary-menu', 'menu_class' => 'menu', 'container_class' => 'menu-header-menu-container', 'fallback_cb' => 'travel_log_primary_menu_fallback' ) ); ?>
		</div>
		</nav>
		<?php
	}

endif;

add_action( 'travel_log_main_nav', 'travel_log_main_menu' );

/* Header Templates Ends */

/* Sidebar template functions Starts. */

if ( ! function_exists( 'travel_log_add_sidebar' ) ) :

	/**
	 * Add slider in pages.
	 *
	 * @since 1.0.0
	 */
	function travel_log_add_sidebar() {

		if ( ( is_front_page() && ! is_home() ) || is_404() || is_search() ) {

			return;
		}

		$defaults = travel_log_default_values();

		$layout = travel_log_get_theme_option( 'travel_log_layout' );

		global $post;

		if ( ! $post ) {
			return;
		}

		$sidebar_meta = get_post_meta( $post->ID, 'travel-log-sidebar-position', true );

		if ( 'full' === $layout && empty( $sidebar_meta ) ) {
			return;
		}

		get_sidebar();
	}

endif;

add_action( 'travel_log_sidebar', 'travel_log_add_sidebar' );

/* Sidebar template functions Ends. */

/* Footer Templates Starts */
if ( ! function_exists( 'travel_log_footer_widgets' ) ) :
	/**
	 * Footer widgets.
	 *
	 * @since 1.0.0
	 */
	function travel_log_footer_widgets() {
		?>
		<div class="container">
		<div class="row">
			<div class="footer-inner-wrapper clearfix">
					<?php

					// Get no of active Sidebars.
					$count = travel_log_active_footer_count();

					$class = '';

					switch ( $count ) {
						case '2':
							$class = '6';
							break;
						case '3':
							$class = '4';
							break;
						case '4':
							$class = '3';
							break;

						default:
							$class = '12';
							break;
					}

					$footer_areas = array( 'footer-sidebar', 'footer-sidebar-2', 'footer-sidebar-3', 'footer-sidebar-4' );

					foreach ( $footer_areas as $footer ) {

						if ( is_active_sidebar( $footer ) ) : ?>

								<div class="col-sm-<?php echo esc_attr( $class ); ?>" >

									<?php dynamic_sidebar( $footer ); ?>

								</div>
							<?php

							endif;

					}

					?>
					</div>
				</div>
			</div>
		<?php
	}

endif;

add_action( 'travel_log_before_footer_copyright', 'travel_log_footer_widgets' );

if ( ! function_exists( 'travel_log_footer_menu' ) ) :
	/**
	 * Footer Menu Area.
	 *
	 * @since 1.0.0
	 */
	function travel_log_footer_menu() {

		$footer_menu_enable = travel_log_get_theme_option( 'footer_menu_enable' );

		if ( true == $footer_menu_enable ) :

		?>
		<div class="container-fluid">
		<div class="row">
			<div class="footer-nav-menu">
				<div class="container">
					<div class="row">
						<?php wp_nav_menu( array( 'theme_location' => 'footer-menu', 'container_class' => 'quick-menu footer', 'fallback_cb' => 'travel_log_primary_menu_fallback' ) ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php

	endif;
	}

endif;

add_action( 'travel_log_before_footer_copyright', 'travel_log_footer_menu', 15 );

if ( ! function_exists( 'travel_log_footer_copyright_content' ) ) :
	/**
	 * Footer Copyright Contents.
	 *
	 * @since 1.0.0
	 */
	function travel_log_footer_copyright_content() {
		?>
		<div class="travel-copyright">
		<?php $footer_copyright_text = travel_log_get_theme_option( 'footer_copyright_txt' );

		if ( ! empty( $footer_copyright_text ) ) :
	?>
	<p>
		<?php echo wp_kses_post( $footer_copyright_text ); ?>
	</p>
	<?php endif; ?>
		<?php if ( true == apply_filters( 'ws_footer_credits_enable', true ) ) : ?>
			<p>
				<?php esc_html_e( 'Proudly powered by', 'travel-log' ); ?> <a href="<?php echo esc_url( __( 'https://wordpress.org/', 'travel-log' ) ); ?>"><?php esc_html_e( 'WordPress', 'travel-log' ); ?></a>

				<span class="sep"> | </span>

					<?php apply_filters( 'ws_footer_credits_text', printf( esc_html__( '%1$s by %2$s.', 'travel-log' ), 'Travel Log', '<a href="https://wensolutions.com/" rel="designer">WEN Solutions</a>' ) ); ?>

			</p>

		<?php endif; ?>
	</div>
	<?php
	}

endif;

add_action( 'travel_log_footer_copyright', 'travel_log_footer_copyright_content' );

/* Footer Templates Ends */
