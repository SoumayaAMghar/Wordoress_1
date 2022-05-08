<?php
/**
 * Core functions.
 *
 * @package Travel_Log
 */

if ( ! function_exists( 'travel_log_get_theme_option' ) ) :

	/**
	 * Get theme option
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Option key.
	 * @return mixed Option value.
	 */
	function travel_log_get_theme_option( $key ) {

		$default_options = travel_log_default_values();

		if ( empty( $key ) ) {
			return;
		}

		$theme_options = (array) get_theme_mod( 'travel_log_options' );
		$theme_options = wp_parse_args( $theme_options, $default_options );

		$value = null;

		if ( isset( $theme_options[ $key ] ) ) {
			$value = $theme_options[ $key ];
		}

		return $value;

	}

endif;

/**
 * [travel_log_remove_customizer_theme_mods Remove theme mods]
 *
 * @return [type] [description]
 */
function travel_log_remove_customizer_theme_mods() {

	$default_options = travel_log_default_values();

	$theme_options = (array) get_theme_mod( 'travel_log_options' );

	$theme_options['slider_content_type'] = $default_options['slider_content_type'];

	$theme_options['post_filter_content_type'] = $default_options['post_filter_content_type'];

	$theme_options['home_recommended_content_type'] = $default_options['home_recommended_content_type'];

	set_theme_mod( $name = 'travel_log_options' , $theme_options );

}

// WP_Travel Plugin deactivation Hook Support.
add_action( 'wp_travel_deactivated', 'travel_log_remove_customizer_theme_mods' );

