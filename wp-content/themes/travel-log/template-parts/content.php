<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Travel_Log
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php travel_log_entry_header() ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
		if ( has_post_thumbnail() ) {?>
			<a href="<?php the_permalink() ?>"><?php the_post_thumbnail( apply_filters( 'travel_log_post_thumbnail_size', 'medium' ) ); ?></a>

		<?php
		}
		?>
		<?php
		if ( is_archive() || is_home() ) {
				the_excerpt();
				echo '<p><a href="' . esc_url( get_the_permalink() ) . '" class="theme-read-more">' . esc_html__( 'Read More', 'travel-log' ) . '</a></p>';
		} else {
			the_content( sprintf(
				/* translators: %s: Name of current post. */
				wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'travel-log' ), array( 'span' => array( 'class' => array() ) ) ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			) );
		}

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'travel-log' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php travel_log_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
