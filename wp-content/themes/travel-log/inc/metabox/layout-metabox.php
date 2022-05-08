<?php
/**
 * Sidebar Position metabox file.
 *
 * @package Travel_Log
 * @since Travel_Log 1.0.7
 */

if ( ! function_exists( 'travel_log_sidebar_position_callback' ) ) :
	/**
	 * Outputs the content of the sidebar position
	 */
	function travel_log_sidebar_position_callback( $post ) {
		wp_nonce_field( basename( __FILE__ ), 'travel_log_nonce' );
		$sidebar_pos = get_post_meta( $post->ID, 'travel-log-sidebar-position', true );
		$selected_sidebar_position = $sidebar_pos;
		if ( 'page' == get_current_screen()->id ) :
			$selected_sidebar_position = ! empty( $sidebar_pos ) ? $sidebar_pos : 'full';
		endif;
		$sidebar_positions   = travel_log_sidebar_position();
		?>

		<p>
		 <label for="travel-log-sidebar-position" class="travel-log-row-title"><?php esc_html_e( 'Sidebar Position', 'travel-log' )?></label>
		 <select name="travel-log-sidebar-position" id="travel-log-sidebar-position">
		  <option value="default" <?php if ( isset( $selected_sidebar_position ) ) { selected( $selected_sidebar_position, 'default' );} ?>><?php esc_html_e( 'Default ( customizer option )', 'travel-log' ); ?></option>

			<?php foreach ( $sidebar_positions as $sidebar_position => $value ) { ?>
			 <option value="<?php echo esc_attr( $sidebar_position );?>" <?php if ( isset( $selected_sidebar_position ) ) { selected( $selected_sidebar_position, $sidebar_position );} ?>><?php echo esc_html( $value ); ?></option>
			<?php } ?>
		 </select>
		</p>
		<?php
	}
endif;

if ( ! function_exists( 'travel_log_meta_save' ) ) :
	/**
	 * Saves the sidebar position input
	 */
	function travel_log_meta_save( $post_id ) {

		// Checks save status.
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST['travel_log_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['travel_log_nonce'] ), basename( __FILE__ ) ) ) ? 'true' : 'false';

		// Exits script depending on save status.
		if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		// Checks for input and sanitizes/saves if needed.
		if ( isset( $_POST['travel-log-sidebar-position'] ) ) {
			update_post_meta( $post_id, 'travel-log-sidebar-position', sanitize_text_field( wp_unslash( $_POST['travel-log-sidebar-position'] ) ) );
		}

		if ( isset( $_POST['travel-log-sidebar'] ) ) {
			update_post_meta( $post_id, 'travel-log-sidebar', sanitize_text_field( wp_unslash( $_POST['travel-log-sidebar'] ) ) );
		}

	}
endif;
add_action( 'save_post', 'travel_log_meta_save' );
