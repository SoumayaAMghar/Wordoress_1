<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Travel_Log
 */

?>
<?php if ( ! is_front_page() ) : ?>
				
	
		</div> <!--container -->


<?php endif; ?>
	
	</div><!-- #container -->
	<footer id="footer" class="footer-widget-area travel-site-footer clearfix" role="contentinfo">
		<div class="travel-site-bottom-footer clearfix">
			<?php
			/**
			 * Hook travel_log_before_footer_copyright.
			 *
			 * @hooked travel_log_footer_widgets - 10
			 * @hooked travel_log_footer_menu - 15
			 */
			do_action( 'travel_log_before_footer_copyright' );
			?>
			<div class="container-fluid">
				<div class="row">

					<div class="copy-right-footer">
						<div class="container">
							<div class="row">

									<?php
									/**
									 * Hook travel_log_footer_copyright.
									 *
									 * @hooked travel_log_footer_copyright_content - 10
									 */
									do_action( 'travel_log_footer_copyright' );
									?>

							</div>
						</div>
					</div>

				</div>
			</div>
		<?php
		/**
		 * Hook travel_log_after_footer_copyright.
		 */
		do_action( 'travel_log_after_footer_copyright' );
		?>
		</div>
	</footer><!-- #colophon -->
	<a href="#page" id="return-to-top"> <i class="wt-icon wt-icon-angle-up"></i></a>
</div><!-- #page -->


<?php wp_footer(); ?>

</body>
</html>
