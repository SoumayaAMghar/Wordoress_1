<?php
/**
 * Itinerary Archive Template
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/archive-itineraries.php.
 *
 * HOWEVER, on occasion wp-travel will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.wensolutions.com/document/template-structure/
 * @author      WenSolutions
 * @package     wp-travel/Templates
 * @since       1.0.0
 */

get_header( 'itinerary' ); ?>
<?php do_action( 'wp_travel_before_main_content' ); ?>
<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php travel_log_itinerary_get_template_part( 'content', 'archive-itineraries' ); ?>
	<?php endwhile; // end of the loop. ?>
<?php else : ?>
	<?php travel_log_itinerary_get_template_part( 'content', 'archive-itineraries-none' ); ?>
<?php endif; ?>
<?php do_action( 'wp_travel_after_main_content' ); ?>
<?php do_action( 'wp_travel_archive_listing_sidebar' ); ?>
<?php get_footer( 'itinerary' ); ?>
