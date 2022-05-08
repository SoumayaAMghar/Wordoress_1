<?php
/**
 * As many pricing related functions in WP Travel 4.4.0 are deprecated,
 * we are making some backward compatible helper functions for the theme.
 *
 * @since 1.2.9
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Get Price Per text.
 *
 * @param int $post_id Current post id.
 */
function travel_log_itinerary_get_price_per_text( $trip_id, $price_key = '', $return_key = false, $category_id = null ) {
	if ( function_exists( 'wptravel_get_price_per_text' ) ) {
		return wptravel_get_price_per_text( $trip_id, $price_key, $return_key, $category_id );
	} elseif ( function_exists( 'wp_travel_get_price_per_text' ) ) {
		return wp_travel_get_price_per_text( $trip_id, $price_key, $return_key, $category_id );
	}
}

/**
 * Get Price Per text.
 *
 * @param int $post_id Current post id.
 */
function travel_log_itinerary_get_formated_price_currency( $price, $regular_price = '', $price_key = '', $post_id = '' ) {
	if ( function_exists( 'wptravel_get_formated_price_currency' ) ) {
		return wptravel_get_formated_price_currency( $price, $regular_price, $price_key, $post_id );
	} elseif ( function_exists( 'wp_travel_get_formated_price_currency' ) ) {
		return wp_travel_get_formated_price_currency( $price, $regular_price, $price_key, $post_id );
	}
}

/**
 * Get Price Per text.
 *
 * @param int $post_id Current post id.
 */
function travel_log_itinerary_get_trip_duration( $trip_id ) {
	if ( function_exists( 'wptravel_get_trip_duration' ) ) {
		return wptravel_get_trip_duration( $trip_id );
	} elseif ( function_exists( 'wp_travel_get_trip_duration' ) ) {
		return wp_travel_get_trip_duration( $trip_id );
	}
}

/**
 * Get Price Per text.
 *
 * @param int $post_id Current post id.
 */
function travel_log_itinerary_tab_show_in_menu( $tab_name ) {
	if ( function_exists( 'wptravel_tab_show_in_menu' ) ) {
		return wptravel_tab_show_in_menu( $tab_name );
	} elseif ( function_exists( 'wp_travel_tab_show_in_menu' ) ) {
		return wp_travel_tab_show_in_menu( $tab_name );
	}
}

/**
 * Get Price Per text.
 *
 * @param int $post_id Current post id.
 */
function travel_log_itinerary_trip_price( $trip_id, $hide_rating = false ) {
	if ( function_exists( 'wptravel_trip_price' ) ) {
		return wptravel_trip_price( $trip_id, $hide_rating );
	} elseif ( function_exists( 'wp_travel_trip_price' ) ) {
		return wp_travel_trip_price( $trip_id, $hide_rating );
	}
}

/**
 * Get Price Per text.
 *
 * @param int $post_id Current post id.
 */
function travel_log_itinerary_get_min_price_key( $options ) {
	if ( function_exists( 'wptravel_get_min_price_key' ) ) {
		return wptravel_get_min_price_key( $options );
	} elseif ( function_exists( 'wp_travel_get_min_price_key' ) ) {
		return wp_travel_get_min_price_key( $options );
	}
}

/**
 * Get Price Per text.
 *
 * @param int $post_id Current post id.
 */
function travel_log_itinerary_search_form() {
	if ( function_exists( 'wptravel_search_form' ) ) {
		return wptravel_search_form();
	} elseif ( function_exists( 'wp_travel_search_form' ) ) {
		return wp_travel_search_form();
	}
}

/**
 * Get Price Per text.
 *
 * @param int $post_id Current post id.
 */
function travel_log_itinerary_get_group_size( $trip_id ) {
	if ( function_exists( 'wptravel_get_group_size' ) ) {
		return wptravel_get_group_size( $trip_id );
	} elseif ( function_exists( 'wp_travel_get_group_size' ) ) {
		return wp_travel_get_group_size( $trip_id );
	}
}

/**
 * Get Price Per text.
 *
 * @param int $post_id Current post id.
 */
function travel_log_itinerary_get_post_placeholder_image_url() {
	if ( function_exists( 'wptravel_get_post_placeholder_image_url' ) ) {
		return wptravel_get_post_placeholder_image_url();
	} elseif ( function_exists( 'wp_travel_get_post_placeholder_image_url' ) ) {
		return wp_travel_get_post_placeholder_image_url();
	}
}

function travel_log_itinerary_is_itinerary( $post_id = '' ) {
	if ( function_exists( 'wptravel_is_itinerary' ) ) {
		return wptravel_is_itinerary( $post_id );
	} elseif ( function_exists( 'wp_travel_is_itinerary' ) ) {
		return wp_travel_is_itinerary( $post_id );
	}
}

/**
 * Return Tabs and its content for single page.
 *
 * @since 1.1.2 Modified in 2.0.7
 *
 * @return void
 */
function travel_log_itinerary_get_frontend_tabs( $show_in_menu_query = '', $frontend_hide_content = '' ) {
	if ( function_exists( 'wptravel_get_frontend_tabs' ) ) {
		return wptravel_get_frontend_tabs( $show_in_menu_query, $frontend_hide_content );
	} elseif ( function_exists( 'wp_travel_get_frontend_tabs' ) ) {
		return wp_travel_get_frontend_tabs( $show_in_menu_query, $frontend_hide_content );
	}
}

/**
 * Return WP Travel Strings.
 *
 * @return void
 */
function travel_log_itinerary_get_strings() {
	if ( function_exists( 'wptravel_get_strings' ) ) {
		return wptravel_get_strings();
	} elseif ( function_exists( 'wp_travel_get_strings' ) ) {
		return wp_travel_get_strings();
	}
}

/**
 * Return WP Travel Strings.
 *
 * @return void
 */
function travel_log_itinerary_trip_rating( $post_id ) {
	if ( function_exists( 'wptravel_trip_rating' ) ) {
		return wptravel_trip_rating( $post_id );
	} elseif ( function_exists( 'wp_travel_trip_rating' ) ) {
		return wp_travel_trip_rating( $post_id );
	}
}

/**
 * Return WP Travel Strings.
 *
 * @return void
 */
function travel_log_itinerary_get_enquiries_form( $trips_dropdown = '' ) {
	if ( function_exists( 'wptravel_get_enquiries_form' ) ) {
		return wptravel_get_enquiries_form( $trips_dropdown );
	} elseif ( function_exists( 'wp_travel_get_enquiries_form' ) ) {
		return wp_travel_get_enquiries_form( $trips_dropdown );
	}
}

/**
 * Get post thumbnail.
 *
 * @param  int    $post_id Post ID.
 * @param  string $size    Image size.
 * @return string          Image tag.
 */
function travel_log_itinerary_get_post_thumbnail( $post_id, $size = 'wp_travel_thumbnail' ) {
	if ( function_exists( 'wptravel_get_post_thumbnail' ) ) {
		return wptravel_get_post_thumbnail( $post_id, $size );
	} elseif ( function_exists( 'wp_travel_get_post_thumbnail' ) ) {
		return wp_travel_get_post_thumbnail( $post_id, $size );
	}
}

/**
 * Get post thumbnail.
 *
 * @param  int    $post_id Post ID.
 * @param  string $size    Image size.
 * @return string          Image tag.
 */
function travel_log_itinerary_get_post_thumbnail_url( $post_id, $size = 'wp_travel_thumbnail' ) {
	if ( function_exists( 'wptravel_get_post_thumbnail_url' ) ) {
		return wptravel_get_post_thumbnail_url( $post_id, $size );
	} elseif ( function_exists( 'wp_travel_get_post_thumbnail_url' ) ) {
		return wp_travel_get_post_thumbnail_url( $post_id, $size );
	}
}

/**
 * Return All Settings of WP travel.
 */
function travel_log_itinerary_get_settings() {
	if ( function_exists( 'wptravel_get_settings' ) ) {
		return wptravel_get_settings();
	} elseif ( function_exists( 'wp_travel_get_settings' ) ) {
		return wp_travel_get_settings();
	}
}

/**
 * Get the average rating of product. This is calculated once and stored in postmeta.
 */
function travel_log_itinerary_get_average_rating( $post_id = null ) {
	if ( function_exists( 'wptravel_get_average_rating' ) ) {
		return wptravel_get_average_rating( $post_id );
	} elseif ( function_exists( 'wp_travel_get_average_rating' ) ) {
		return wp_travel_get_average_rating( $post_id );
	}
}

/**
 * Function to get currency symbol or name.
 */
function travel_log_itinerary_get_currency_symbol( $currency_code = null ) {
	if ( function_exists( 'wptravel_get_currency_symbol' ) ) {
		return wptravel_get_currency_symbol( $currency_code );
	} elseif ( function_exists( 'wp_travel_get_currency_symbol' ) ) {
		return wp_travel_get_currency_symbol( $currency_code );
	}
}

/**
 * Function to get currency symbol or name.
 */
function travel_log_itinerary_get_rating_count( $value = '' ) {
	if ( function_exists( 'wptravel_get_rating_count' ) ) {
		return wptravel_get_rating_count( $value );
	} elseif ( function_exists( 'wp_travel_get_rating_count' ) ) {
		return wp_travel_get_rating_count( $value );
	}
}

/**
 * Get Template Part.
 *
 * @param  String $slug Name of slug.
 * @param  string $name Name of file / template.
 */
function travel_log_itinerary_get_template_part( $slug, $name = '' ) {
	if ( function_exists( 'wptravel_get_template_part' ) ) {
		return wptravel_get_template_part( $slug, $name );
	} elseif ( function_exists( 'wp_travel_get_template_part' ) ) {
		return wp_travel_get_template_part( $slug, $name );
	}
}

function travel_log_itinerary_get_price( $trip_id, $is_regular_price = false, $pricing_id = '', $category_id = '', $price_key = '' ) {

	if ( method_exists( 'WP_Travel_Helpers_Pricings', 'get_price' ) ) {

		/**
		 * Support for WP Travel 4.4.0 and greater.
		 */
		$args = array(
			'trip_id'          => $trip_id,
			'is_regular_price' => $is_regular_price,
			'pricing_id'       => $pricing_id,
			'category_id'      => $category_id,
			'price_key'        => $price_key,
		);
		return WP_Travel_Helpers_Pricings::get_price( $args );
	} else {
		return wp_travel_get_price( $trip_id, $is_regular_price, $pricing_id, $category_id, $price_key );
	}
}

function travel_log_itinerary_is_sale_enabled( $trip_id, $from_price_sale_enable = false, $pricing_id = '', $category_id = '', $price_key = '' ) {

	if ( method_exists( 'WP_Travel_Helpers_Trips', 'is_sale_enabled' ) ) {

		/**
		 * Support for WP Travel 4.4.0 and greater.
		 */
		$args = array(
			'trip_id'                => $trip_id,
			'from_price_sale_enable' => $from_price_sale_enable,
			'pricing_id'             => $pricing_id,
			'category_id'            => $category_id,
			'price_key'              => $price_key,
		);
		return WP_Travel_Helpers_Trips::is_sale_enabled( $args );
	} else {
		return wp_travel_is_enable_sale_price( $trip_id, $from_price_sale_enable, $pricing_id, $category_id, $price_key );
	}
}

function travel_log_itinerary_get_sale_price( $trip_id = 0 ) {

	if ( ! $trip_id ) {
		return 0;
	}

	if ( method_exists( 'WP_Travel_Helpers_Pricings', 'get_price' ) ) {

		/**
		 * Support for WP Travel 4.4.0 and greater.
		 */
		$args = array(
			'trip_id'          => $trip_id,
			'is_regular_price' => true,
		);
		return WP_Travel_Helpers_Pricings::get_price( $args );
	} else {
		return wp_travel_get_trip_sale_price( $trip_id );
	}

}
