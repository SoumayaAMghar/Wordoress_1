<div class="dpsp-page-wrapper dpsp-page-toolkit wrap <?php echo Social_Pug::is_free() ? 'dpsp-page-free' : 'dpsp-page-pro'; ?>">

	<?php wp_nonce_field( 'dpsptkn', 'dpsptkn' ); ?>

	<!-- Share Tools -->
	<h1 class="dpsp-page-title"><?php esc_html_e( 'Social Share Tools', 'social-pug' ); ?></h1>

	<div class="dpsp-row dpsp-m-padding">
	<?php
		$tools = dpsp_get_tools( 'share_tool' );

		foreach ( $tools as $tool_slug => $tool ) {
			dpsp_output_tool_box( $tool_slug, $tool );
		}
	?>
	</div><!-- End of Share Tools -->

	<?php do_action( 'dpsp_page_toolkit_after_share_tools' ); ?>

	<!-- Follow Tools -->
	<?php $tools = dpsp_get_tools( 'follow_tool' ); ?>
	<?php if ( count( $tools ) ) : ?>
	<h1 class="dpsp-page-title"><?php esc_html_e( 'Social Follow Tools', 'social-pug' ); ?></h1>
	<div class="dpsp-row dpsp-m-padding">
	<?php
		foreach ( $tools as $tool_slug => $tool ) {
			dpsp_output_tool_box( $tool_slug, $tool );
		}
	?>
	</div><?php endif; ?><!-- End of Follow Tools -->

	<?php do_action( 'dpsp_page_toolkit_after_follow_tools' ); ?>

	<!-- Misc Tools -->
	<?php $tools = dpsp_get_tools( 'misc_tool' ); ?>
	<?php if ( count( $tools ) ) : ?>
	<h1 class="dpsp-page-title"><?php esc_html_e( 'Misc Tools', 'social-pug' ); ?></h1>

	<div class="dpsp-row dpsp-m-padding">
	<?php
		foreach ( $tools as $tool_slug => $tool ) {
			dpsp_output_tool_box( $tool_slug, $tool );
		}
	?>
	</div><?php endif; ?><!-- End of Misc Tools -->

	<?php do_action( 'dpsp_page_toolkit_after_misc_tools' ); ?>

</div>

<?php do_action( 'dpsp_submenu_page_bottom' ); ?>
