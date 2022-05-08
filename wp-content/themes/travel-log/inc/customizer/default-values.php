<?php
/**
 * Default values for Customizer fields.
 *
 * @package Travel_Log
 */

/**
 * Default values for theme.
 *
 * @return array Default values.
 */
function travel_log_default_values() {
	$defaults['header_text_color'] = esc_attr( '#555' );
	$defaults['background_color'] = esc_attr( '#f9f9f9' );
	$defaults['travel_log_layout'] = esc_html__( 'right', 'travel-log' );

	// Slider posts.
	$defaults['home_slider_enable'] = false;
	$defaults['home_itinerary_search_enable'] = false;
	$defaults['home_slider_category'] = '';
	$defaults['home_slider_speed'] = 600;
	$defaults['home_slider_read_more_text'] = esc_html__( 'MORE INFO', 'travel-log' );
	$defaults['slider_content_type'] = 'category';
	$defaults['home_slider_category_trip_type'] = '';
	$defaults['home_slider_category_trip_location'] = '';
	$defaults['home_slider_book_now_enable'] = true;

	// Itinerary Search.
	$defaults['itinerary_search_title'] = esc_html__( 'Search your best tour', 'travel-log' );
	$defaults['itinerary_search_sub_title'] = esc_html__( 'Find your perfect tour with your preferences !!', 'travel-log' );

	// Post filter.
	$defaults['post_filter_enable'] = false;
	$defaults['post_filter_title'] = esc_html__( 'Find your perfect tour', 'travel-log' );
	$defaults['post_filter_sub_title'] = esc_html__( 'With special offers and discounts we provide best and cheap tour packages for different countries. View our Hottest package for the year.', 'travel-log' );
	$defaults['post_filter_content_type'] = 'category';
	$defaults['post_filter_category_trip_type'] = '';
	$defaults['post_filter_category_trip_location'] = '';
	$defaults['post_filter_category'] = '';

	// Call to action.
	$defaults['call_to_action_enable'] = false;
	$defaults['call_to_action_content_page'] = '';
	$defaults['call_to_action_button_text'] = esc_html__( 'Book Now', 'travel-log' );
	$defaults['call_to_action_excerpt_length'] = absint( '20' );

	// Recommended posts.
	$defaults['recommended_posts_enable'] = false;
	$defaults['recommended_posts_title'] = esc_html__( 'Recommended Trips', 'travel-log' );
	$defaults['recommended_posts_sub_title'] = esc_html__( 'Here are some of our recomended trips for the year.', 'travel-log' );
	$defaults['recommended_posts_category'] = '';
	$defaults['home_recommended_content_type'] = 'category';

	// Testimonials.
	$defaults['testimonials_enable'] = false;
	$defaults['testimonials_title'] = esc_html__( 'What our customers are saying about us?', 'travel-log' );
	$defaults['testimonials_bg_url'] = '';
	$defaults['testimonials_category'] = '';
	$defaults['testimonials_speed'] = 600;
	$defaults['home_testimonials_content_type'] = 'post';

	// Latest posts.
	$defaults['latest_posts_enable'] = false;
	$defaults['latest_posts_title'] = esc_html__( 'latest blog posts', 'travel-log' );
	$defaults['latest_posts_sub_title'] = esc_html__( 'Read our blog and get an update on every Trips and Tours.', 'travel-log' );
	$defaults['latest_posts_speed'] = 600;
	$defaults['latest_posts_excluded_cats'] = '';

	// Theme Options.
	$defaults['travel_log_breadcrumb'] = false;
	$defaults['travel_log_loader'] = false;
	$defaults['travel_log_social_share_disable'] = false;
	$defaults['sharer_fb_enable'] = true;
	$defaults['sharer_twitter_enable'] = true;
	$defaults['sharer_gplus_enable'] = true;
	$defaults['header_banner_image'] = '';
	$defaults['travel_log_enable_header_wp_travel_search'] = true;

	// Footer Options.
	$defaults['footer_menu_enable'] = false;
	$defaults['footer_copyright_txt'] = esc_html__( '&copy; All Rights Reserved.', 'travel-log' );

	// Home Contents.
	$defaults['enable_home_content'] = true;

	// Colors.
	$defaults['travel_log_link_color'] = '#f83531';
	$defaults['travel_log_link_hover_color'] = '#df1814';
	$defaults['travel_log_button_color'] = '#f83531';
	$defaults['travel_log_button_hover_color'] = '#df1814';
	$defaults['travel_log_button_text_color'] = '#ffffff';
	$defaults['travel_log_button_text_hover_color'] = '#ffffff';
	$defaults['travel_log_footer_bg_color'] = '#252525';

	return $defaults;
}
