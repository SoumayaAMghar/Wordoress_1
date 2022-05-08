<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Travel_Log
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<!-- Instruct Internet Explorer to use its latest rendering engine -->
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php endif; ?>

<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>
<?php

if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}

	$loader_status = travel_log_get_theme_option( 'travel_log_loader' );

	$loader_class = 'loader-inactive';

if ( false === $loader_status ) :

		$loader_class = 'loader-active';

	?>
		<div id="onload" class="<?php echo esc_attr( $loader_class ); ?>" >
			<div id="loader">
				<div id="fountainG_1" class="fountainG"></div>
				<div id="fountainG_2" class="fountainG"></div>
				<div id="fountainG_3" class="fountainG"></div>
				<div id="fountainG_4" class="fountainG"></div>
				<div id="fountainG_5" class="fountainG"></div>
				<div id="fountainG_6" class="fountainG"></div>
				<div id="fountainG_7" class="fountainG"></div>
				<div id="fountainG_8" class="fountainG"></div>
			</div>
		</div>

	<?php endif; ?>

		<div id="page" class="site animate-bottom">
			<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'travel-log' ); ?></a>

			<?php
			/**
			 * Hook before_header.
			 *
			 * @hooked travel_log_top_header - 10
			 */
			do_action( 'before_header' );
			?>

			<?php
			/**
			 * Hook travel_log_header.
			 *
			 * @hooked travel_log_show_header - 10
			 */
			do_action( 'travel_log_header' );
			?>

			<?php do_action( 'after_header' ); ?>

			<!-- Breadcrumbs -->

				<?php

				$breadcrumb_status = travel_log_get_theme_option( 'travel_log_breadcrumb' );

				if ( true !== $breadcrumb_status && !travel_log_is_singular_block_enabled() ) :

					/**
					 * Support for for yoast breadcrumb.
					 *
					 * @since 1.2.7
					 */
					$use_yoast_breadcrumbs = function_exists( 'yoast_breadcrumb' ) && yoast_breadcrumb( '', '', false ) ? true : false;

					if ( ! is_front_page() ) :

						/**
						 * Support for for yoast breadcrumb.
						 *
						 * @since 1.2.7
						 */
						if ( $use_yoast_breadcrumbs ) {
							yoast_breadcrumb( '<div id="breadcrumb"><div class="container">', '</div></div><!-- Breadcrumbs-end -->' );
						} else {
							/**
							 * Default breadcrumb.
							 *
							 * @since 1.0.0
							 */
							?>
							<div id="breadcrumb">
								<div class="container">
									<?php
										echo breadcrumb_trail(
											$args = array(
												'container'   => 'div',
												'show_browse' => false,
											)
										);
									?>
								</div>
							</div>
							<!-- Breadcrumbs-end -->
							<?php
						}
					endif;
				endif;
				if ( ! is_front_page() || is_home() ) :
					?>

				<div id="content" class="site-content">

					<?php if ( ! is_singular( 'itineraries' ) ) : ?>

						<div class="container">
					
					<?php endif; ?>

					<?php
				endif;
