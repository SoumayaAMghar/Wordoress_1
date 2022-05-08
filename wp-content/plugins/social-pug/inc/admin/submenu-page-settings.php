<?php

/**
 * Function that creates the sub-menu item and page for the settings page.
 */
function dpsp_register_settings_subpage() {
	add_submenu_page( 'dpsp-social-pug', __( 'Settings', 'social-pug' ), __( 'Settings', 'social-pug' ), 'manage_options', 'dpsp-settings', 'dpsp_settings_subpage' );
}

/**
 * Outputs content to the settings subpage.
 */
function dpsp_settings_subpage() {
	$tabs = [
		'general-settings' => __( 'General Settings', 'social-pug' ),
		'social-identity'  => __( 'Social Identity', 'social-pug' ),
	];

	$tabs = apply_filters( 'dpsp_submenu_page_settings_tabs', $tabs );

	$pro = ( \Social_Pug::is_free() ) ? '' : '-pro';
	include DPSP_PLUGIN_DIR . '/inc/admin/views/view-submenu-page-settings' . $pro . '.php';
}

/**
 *
 */
function dpsp_settings_register_settings() {
	register_setting( 'dpsp_settings', 'dpsp_settings', 'dpsp_settings_sanitize' );
}

/**
 * Filter and sanitize settings.
 *
 * @param array $new_settings Incoming settings values.
 * @return array
 */
function dpsp_settings_sanitize( $new_settings ) {
	// Explicitly save a value for the inline_critical_css setting.
	if ( ! isset( $new_settings['inline_critical_css'] ) ) {
		$new_settings['inline_critical_css'] = '1';
	}

	return $new_settings;
}

/**
 * Hooks to generate a Facebook App access token that will be used for retrieving share counts.
 */
function dpsp_generate_facebook_app_access_token( $new_settings = [], $old_settings = [] ) {

	if ( empty( $new_settings['facebook_app_id'] ) || empty( $new_settings['facebook_app_secret'] ) ) {
		return $new_settings;
	}

	$response = wp_remote_post(
		add_query_arg(
			[
				'client_id'     => trim( $new_settings['facebook_app_id'] ),
				'client_secret' => trim( $new_settings['facebook_app_secret'] ),
				'grant_type'    => 'client_credentials',
			],
			'https://graph.facebook.com/oauth/access_token'
		)
	);

	if ( is_wp_error( $response ) ) {
		return $new_settings;
	}

	if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
		return $new_settings;
	}

	$body = wp_remote_retrieve_body( $response );
	$body = json_decode( $body, true );

	if ( ! empty( $body['access_token'] ) && strpos( $body['access_token'], '|' ) !== false ) {
		$new_settings['facebook_app_access_token'] = $body['access_token'];
	}

	return $new_settings;
}

/**
 * Hooks to update settings to check the serial status and update it.
 */
function dpsp_update_serial_key_status( $old_settings = [], $new_settings = [] ) {

	$serial = ( isset( $new_settings['product_serial'] ) ? $new_settings['product_serial'] : '' );

	// Get serial status
	$serial_status = dpsp_get_serial_key_status( $serial );

	if ( ! is_null( $serial_status ) ) {
		update_option( 'dpsp_product_serial_status', $serial_status );
	} else {
		update_option( 'dpsp_product_serial_status', '' );
	}
}

/**
 * Adds a validation icon for the serial key.
 */
function dpsp_add_serial_status_icon( $slug, $type, $name ) {

	if ( 'serial-key' === $slug ) {
		return;
	}

	$dpsp_settings      = Mediavine\Grow\Settings::get_setting( 'dpsp_settings', [] );
	$dpsp_serial_status = Mediavine\Grow\Settings::get_setting( 'dpsp_product_serial_status', '' );

	if ( Mediavine\Grow\Settings::get_setting( 'mv_grow_license', false ) ) {
		return;
	}

	if ( empty( $dpsp_settings['product_serial'] ) && empty( $dpsp_serial_status ) ) {
		return;
	}

	switch ( $dpsp_serial_status ) {
		case 1:
		case 2:
			echo '<div id="dpsp-serial-key-status" class="dpsp-valid"><span title="' . esc_html__( 'Serial key is valid.', 'social-pug' ) . '" class="dashicons dashicons-yes"></span><span>' . esc_html__( 'Serial key is valid.', 'social-pug' ) . '</span></div>';
			break;
		default:
			echo '<div id="dpsp-serial-key-status" class="dpsp-invalid"><span title="' . esc_html__( 'Serial key is invalid or expired.', 'social-pug' ) . '" class="dashicons dashicons-warning"></span><span>' . esc_html__( 'Serial key is invalid or expired.', 'social-pug' ) . '</span></div>';
			break;
	}
}

/**
 * Register hooks for submenu-page-settings.php.
 */
function dpsp_register_admin_settings() {
	add_action( 'admin_menu', 'dpsp_register_settings_subpage', 100 );
	add_action( 'admin_init', 'dpsp_settings_register_settings' );
	add_filter( 'pre_update_option_dpsp_settings', 'dpsp_generate_facebook_app_access_token', 10, 2 );
	add_action( 'add_option_dpsp_settings', 'dpsp_update_serial_key_status', 10, 2 );
	add_action( 'update_option_dpsp_settings', 'dpsp_update_serial_key_status', 10, 2 );
	add_action( 'dpsp_inner_after_settings_field', 'dpsp_add_serial_status_icon', 10, 3 );
}
