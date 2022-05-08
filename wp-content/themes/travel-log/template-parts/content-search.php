<?php
/**
 * Template part for displaying results in search pages
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

	<div class="entry-summary">
	<?php
	if ( has_post_thumbnail() ) {
		echo '<div class="col-sm-4">';
		the_post_thumbnail( 'medium' );
		echo '</div>';
	}
	?>
		<div class="<?php echo esc_attr( has_post_thumbnail() ? 'col-sm-8' : '' ); ?>">
			<?php
			the_excerpt();
			echo '<p><a href="' . esc_url( get_the_permalink() ) . '" class="theme-read-more">' . esc_html__( 'Read More', 'travel-log' ) . '</a></p>';
			?>
		</div>
	</div><!-- .entry-summary -->
	<div class="col-sm-12">
		<div class="row">
			<footer class="entry-footer">
				
				<?php travel_log_entry_footer(); ?>
			
			</footer><!-- .entry-footer -->
		</div>
	</div>
</article><!-- #post-## -->
