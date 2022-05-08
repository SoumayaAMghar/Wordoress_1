<?php
/**
 * Customizer Callback Functions
 *
 * @package Travel_Log
 *
 * @since 1.0.0
 */

if ( ! function_exists( 'travel_log_is_social_share_active' ) ) :

	/**
	 * Check if featured page content is active.
	 *
	 * @since 1.0.7
	 *
	 * @param WP_Customize_Control $control WP_Customize_Control instance.
	 *
	 * @return bool Whether the control is active to the current preview.
	 */
	function travel_log_is_social_share_active( $control ) {

		if ( false === $control->manager->get_setting( 'travel_log_options[travel_log_social_share_disable]' )->value() ) {
			return true;
		} else {
			return false;
		}

	}

endif;

if ( ! function_exists( 'travel_log_is_post_category_selected' ) ) :

	/**
	 * Check if featured page content is active.
	 *
	 * @since 1.0.7
	 *
	 * @param WP_Customize_Control $control WP_Customize_Control instance.
	 *
	 * @return bool Whether the control is active to the current preview.
	 */
	function travel_log_is_post_category_selected( $control ) {

		if ( 'category' === $control->manager->get_setting( 'travel_log_options[post_filter_content_type]' )->value() ) {
			return true;
		} else {
			return false;
		}

	}

endif;

if ( ! function_exists( 'travel_log_is_slider_post_category_selected' ) ) :

	/**
	 * Check if featured page content is active.
	 *
	 * @since 1.0.7
	 *
	 * @param WP_Customize_Control $control WP_Customize_Control instance.
	 *
	 * @return bool Whether the control is active to the current preview.
	 */
	function travel_log_is_slider_post_category_selected( $control ) {

		if ( 'category' === $control->manager->get_setting( 'travel_log_options[slider_content_type]' )->value() ) {
			return true;
		} else {
			return false;
		}

	}

endif;

if ( ! function_exists( 'travel_log_is_slider_itinerary_posts_selected' ) ) :

	/**
	 * Check if featured page content is active.
	 *
	 * @since 1.0.9
	 *
	 * @param WP_Customize_Control $control WP_Customize_Control instance.
	 *
	 * @return bool Whether the control is active to the current preview.
	 */
	function travel_log_is_slider_itinerary_posts_selected( $control ) {

		if ( 'category' !== $control->manager->get_setting( 'travel_log_options[slider_content_type]' )->value() ) {
			return true;
		} else {
			return false;
		}

	}

	endif;

if ( ! function_exists( 'travel_log_is_recommended_post_category_selected' ) ) :

	/**
	 * Check if featured page content is active.
	 *
	 * @since 1.0.7
	 *
	 * @param WP_Customize_Control $control WP_Customize_Control instance.
	 *
	 * @return bool Whether the control is active to the current preview.
	 */
	function travel_log_is_recommended_post_category_selected( $control ) {

		if ( 'category' === $control->manager->get_setting( 'travel_log_options[home_recommended_content_type]' )->value() ) {
			return true;
		} else {
			return false;
		}

	}

endif;


if ( ! function_exists( 'travel_log_is_trip_type_selected' ) ) :

	/**
	 * Check if featured page content is active.
	 *
	 * @since 1.0.7
	 *
	 * @param WP_Customize_Control $control WP_Customize_Control instance.
	 *
	 * @return bool Whether the control is active to the current preview.
	 */
	function travel_log_is_trip_type_selected( $control ) {

		if ( 'trip-types' === $control->manager->get_setting( 'travel_log_options[post_filter_content_type]' )->value() ) {
			return true;
		} else {
			return false;
		}

	}

endif;


if ( ! function_exists( 'travel_log_slider_is_trip_type_selected' ) ) :

	/**
	 * Check if featured page content is active.
	 *
	 * @since 1.0.7
	 *
	 * @param WP_Customize_Control $control WP_Customize_Control instance.
	 *
	 * @return bool Whether the control is active to the current preview.
	 */
	function travel_log_slider_is_trip_type_selected( $control ) {

		if ( 'trip-types' === $control->manager->get_setting( 'travel_log_options[slider_content_type]' )->value() ) {
			return true;
		} else {
			return false;
		}

	}

endif;

if ( ! function_exists( 'travel_log_is_trip_location_selected' ) ) :

	/**
	 * Check if featured page content is active.
	 *
	 * @since 1.0.7
	 *
	 * @param WP_Customize_Control $control WP_Customize_Control instance.
	 *
	 * @return bool Whether the control is active to the current preview.
	 */
	function travel_log_is_trip_location_selected( $control ) {

		if ( 'trip-location' === $control->manager->get_setting( 'travel_log_options[post_filter_content_type]' )->value() ) {
			return true;
		} else {
			return false;
		}

	}

endif;


if ( ! function_exists( 'travel_log_is_slider_trip_location_selected' ) ) :

	/**
	 * Check if featured page content is active.
	 *
	 * @since 1.0.7
	 *
	 * @param WP_Customize_Control $control WP_Customize_Control instance.
	 *
	 * @return bool Whether the control is active to the current preview.
	 */
	function travel_log_is_slider_trip_location_selected( $control ) {

		if ( 'trip-location' === $control->manager->get_setting( 'travel_log_options[slider_content_type]' )->value() ) {
			return true;
		} else {
			return false;
		}

	}

endif;

if ( ! function_exists( 'travel_log_is_jetpack_custom_content_module_active' ) ) :

		/**
		 * Check if Jetpack Custom Content Module is active.
		 *
		 * @since 1.0.8
		 *
		 * @return bool Whether the control is active to the current preview.
		 */
	function travel_log_is_jetpack_custom_content_module_active() {

		if (  class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'custom-content-types' ) ) {
				return true;
		} else {
				return false;
		}

	}

	endif;

if ( ! function_exists( 'travel_log_is_wp_travel_plugin_active' ) ) :

		/**
		 * Check if Jetpack Custom Content Module is active.
		 *
		 * @since 1.0.8
		 *
		 * @return bool Whether the control is active to the current preview.
		 */
	function travel_log_is_wp_travel_plugin_active() {

		if (  class_exists( 'WP_Travel' ) ) {
				return true;
		} else {
				return false;
		}

	}
endif;

if ( ! function_exists( 'travel_log_is_testimonial_post_selected' ) ) :

	/**
	 * Check if posts is selected in testimonial content.
	 *
	 * @since 1.0.8
	 *
	 * @param WP_Customize_Control $control WP_Customize_Control instance.
	 *
	 * @return bool Whether the control is active to the current preview.
	 */
	function travel_log_is_testimonial_post_selected( $control ) {

		if ( 'post' === $control->manager->get_setting( 'travel_log_options[home_testimonials_content_type]' )->value() ) {
			return true;
		} else {
			return false;
		}

	}

endif;

if ( ! function_exists( 'travel_log_callback_is_itinerary_search_enable' ) ) :

	/**
	 * Check if Jetpack Custom Content Module is active.
	 *
	 * @param WP_Customize_Control $control WP_Customize_Control instance.
	 * @since 1.0.8
	 *
	 * @return bool Whether the control is active to the current preview.
	 */
	function travel_log_callback_is_itinerary_search_enable( $control ) {

		if (  class_exists( 'WP_Travel' ) && true === $control->manager->get_setting( 'travel_log_options[home_itinerary_search_enable]' )->value() ) {
				return true;
		} else {
				return false;
		}

	}
endif;
