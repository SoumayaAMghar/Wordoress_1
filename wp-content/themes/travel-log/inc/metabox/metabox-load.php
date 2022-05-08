<?php
/**
 * Travel_Log metabox file.
 *
 * This is the template that includes all the other files for metaboxes of Travel_Log theme
 *
 * @package Travel_Log
 * @since Travel_Log 1.0.7
 */

// Include slider layout meta.
require get_template_directory() . '/inc/metabox/layout-metabox.php';

// Include header image meta.
require get_template_directory() . '/inc/metabox/header-image-metabox.php';

if ( ! function_exists( 'travel_log_custom_meta' ) ) :
	/**
	 * Adds meta box to the post editing screen
	 */
	function travel_log_custom_meta() {
		$post_type = array( 'post', 'page' );

		// Sidebar layout meta.
	    add_meta_box( 'travel_log_sidebar_layout_meta', esc_html__( 'Sidebar Layout Options', 'travel-log' ), 'travel_log_sidebar_position_callback', $post_type );

		// // Header image meta
	    add_meta_box( 'travel_log_header_image', esc_html__( 'Header Image Options', 'travel-log' ), 'travel_log_header_image_callback', $post_type );
	}
endif;
add_action( 'add_meta_boxes', 'travel_log_custom_meta' );
