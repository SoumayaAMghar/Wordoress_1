<?php
/**
 * Single themplate functions.
 *
 * @package Travel_Log
 */

/**
 * Single post heading.
 */
function travel_log_single_post_heading() {
	// if ( travel_log_is_singular_block_enabled() ) {
	// 	return;
	// }
	if ( ! is_singular() || is_home() || is_front_page() ) {
		return;
	}
	global $post;
	$author_id = $post->post_author;
	?>
	<div class="custom-header">
		<div class="container">
			<div class="row">
				<header class="entry-header">
					<h1 class="entry-title"><?php the_title();?></h1>
					<?php if ( 'post' === get_post_type() ) : ?>
						<div class="entry-meta">
							<?php travel_log_posted_on(); ?>
						</div><!-- .entry-meta -->
					<?php endif; ?>
				</header><!-- .entry-header -->
			</div>
		</div>
	</div>
	<?php
}
add_action( 'after_header', 'travel_log_single_post_heading' );

/**
 * 404 page heading.
 */
function travel_log_404_heading() {
	if ( ! is_404() ) {
		return;
	}
	?>
	<div class="custom-header">
		<div class="container">
			<div class="row">
				<header class="entry-header">
					<h1 class="entry-title"><?php esc_html_e( '404 Not Found', 'travel-log' );?></h1>
				</header><!-- .entry-header -->
			</div>
		</div>
	</div>
	<?php
}
add_action( 'after_header', 'travel_log_404_heading' );

/**
 * Archive page heading.
 */
function travel_log_archive_heading() {
	if ( ! is_archive() || is_post_type_archive( 'itineraries' ) ) {
		return;
	}
	?>
	<div class="custom-header">
		<div class="container">
			<div class="row">
				<header class="entry-header">
					<?php
						the_archive_title( '<h1 class="entry-title">', '</h1>' );
						the_archive_description( '<div class="archive-description">', '</div>' );
					?>
				</header><!-- .entry-header -->
			</div>
		</div>
	</div>
	<?php
}
add_action( 'after_header', 'travel_log_archive_heading' );

/**
 * Search page heading.
 */
function travel_log_search_heading() {
	if ( ! is_search() ) {
		return;
	}
	?>
	<div class="custom-header">
		<div class="container">
			<div class="row">
				<header class="entry-header">
					<h1 class="entry-title"><?php printf( esc_html__( 'Search Results for: %s', 'travel-log' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
				</header><!-- .entry-header -->
			</div>
		</div>
	</div>
	<?php
}
add_action( 'after_header', 'travel_log_search_heading' );

/**
 * Add featured image in single post.
 */
function travel_log_single_featured_image() {
	if ( has_post_thumbnail() ) {
		the_post_thumbnail( 'full' );
	}
}
add_action( 'travel_log_single_before_entry_content', 'travel_log_single_featured_image' );
