<?php
/**
 * Theme template to display home sections.
 *
 * @package Travel_Log
 */

get_header();
?>
	<div id="primary" class="content-area">
		
		<main id="main" class="site-main" role="main">
			
			<?php if ( travel_log_has_front_page() ) : ?>
				
				<div class="front-page-home-sections">

				<?php

					/**
					 * Hook : Front Page Sections.
					 *
					 * @hooked : travel_log_front_page_slider_wrap - 10
					 * @hooked : travel_log_front_page_post_filter_wrap - 20
					 * @hooked : travel_log_front_page_call_to_action_wrap - 30
					 * @hooked : travel_log_front_page_recommended_posts_wrap - 40
					 * @hooked : travel_log_front_page_testimonials_wrap - 50
					 * @hooked : travel_log_front_page_latest_posts_wrap - 60
					 */
					
					$action_hook = apply_filters( 'travel_log_front_page_sections_filter', 'travel_log_front_page_content' );

				 	do_action( $action_hook );

				?>

				 </div> <!-- #front-page-home-sections -->
			<?php 

				endif;

			?>
			<?php
			if ( have_posts() ) :

				if ( is_home() && ! is_front_page() ) : ?>
					<header>
						<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
					</header>

				<?php
				endif;

				/* Start the Loop */
				while ( have_posts() ) : the_post(); 
				?>
			
				<?php 

					$content_enable = travel_log_get_theme_option( 'enable_home_content' );

					if ( travel_log_has_front_page() ) : ?>
						
						<?php if ( true == $content_enable ) : ?>

							<div id="travel-log-static-content" class="travel-log-static-page-content">
			        			
			        			<div class="container">

									<?php get_template_part( 'template-parts/content', get_post_format() ); ?>

								</div> <!-- .container -->
							
							</div> <!-- #travel-log-static-content -->

						<?php endif; ?>

					<?php 

				else :

						get_template_part( 'template-parts/content', get_post_format() );

				endif; ?>

				<?php

				endwhile;

				the_posts_navigation();

			else :

				get_template_part( 'template-parts/content', 'none' );

			endif; ?>

		</main><!-- #main -->
	
	</div><!-- #primary -->
	<?php

	if ( ! travel_log_has_front_page() ) :
			/**
			 * Hook - travel_log_sidebar.
			 *
			 * @hooked travel_log_add_sidebar -  10
			 */
			do_action( 'travel_log_sidebar' );

	endif;

	get_footer();
