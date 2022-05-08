<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Travel_Log
 */

get_header(); ?>
	<div class="row">
		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">

			<?php
			while ( have_posts() ) : the_post();

				get_template_part( 'template-parts/content', 'single' );

				the_post_navigation(array(
					'prev_text' => esc_html__( 'Prev', 'travel-log' ),
					'next_text' => esc_html__( 'Next', 'travel-log' ),

				));

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					comments_template();
				endif;

			endwhile; // End of the loop.
			?>

			</main><!-- #main -->
		</div><!-- #primary -->

		<?php
		/**
		 * Hook - travel_log_sidebar.
		 *
		 * @hooked travel_log_add_sidebar -  10
		 */
		do_action( 'travel_log_sidebar' );
		?>
	</div>
<?php
get_footer();
