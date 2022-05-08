<?php
/**
 * Front page sections.
 *
 * @package Travel_Log
 */

// Home page panel.
$wp_customize->add_panel( 'travel_log_homepage_panel', array(
	'priority'       => 30,
	'capability'     => 'edit_theme_options',
	'theme_supports' => '',
	'title'          => esc_html__( 'Homepage Sections', 'travel-log' ),
	'description'    => '',
) );

// Section: Slider.
$wp_customize->add_section( 'travel_log_homepage_slider_section' , array(
	'title'       => esc_html__( 'Slider Section', 'travel-log' ),
	'priority'    => 20,
	'panel'  => 'travel_log_homepage_panel',
) );

// Diable field setting.
$wp_customize->add_setting('travel_log_options[home_slider_enable]', array(
	'default'        => $defaults['home_slider_enable'],
	'sanitize_callback' => 'travel_log_sanitize_checkbox',
));

// enable field control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_options[home_slider_enable]', array(
	'label'    => esc_html__( 'Enable Section', 'travel-log' ),
	'section'  => 'travel_log_homepage_slider_section',
	'settings' => 'travel_log_options[home_slider_enable]',
	'type'     => 'checkbox',
	'priority'    => 21,
) ) );

// Add Trip Filter Content Type Setting / Control.
$wp_customize->add_setting( 'travel_log_options[slider_content_type]', array(
	'default'           => $defaults['slider_content_type'],
	'sanitize_callback' => 'travel_log_sanitize_choices',
) );

$wp_customize->add_control( 'travel_log_options[slider_content_type]', array(
	'label'           	=> esc_html__( 'Content Type', 'travel-log' ),
	'section'         	=> 'travel_log_homepage_slider_section',
	'type'            	=> 'select',
	'priority'   		=> 21,
	'choices'         	=> travel_log_customizer_content_type_choices(),
) );

// Category setting.
$wp_customize->add_setting('travel_log_options[home_slider_category]', array(
	'default' => $defaults['home_slider_category'],
	'sanitize_callback' => 'travel_log_sanitize_category',
));

// Category control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_category_slider', array(
	'type' => 'select',
	'label' => esc_html__( 'Select Category', 'travel-log' ),
	'section'  => 'travel_log_homepage_slider_section',
	'settings' => 'travel_log_options[home_slider_category]',
	'choices' => travel_log_list_category(),
	'priority'    => 22,
	'active_callback' => 'travel_log_is_slider_post_category_selected',
)));

// Category setting.
$wp_customize->add_setting('travel_log_options[home_slider_category_trip_type]', array(
	'default' => $defaults['home_slider_category_trip_type'],
	'sanitize_callback' => 'travel_log_sanitize_multiple_dropdown_taxonomies',
));

// Category control.
$wp_customize->add_control(
	new Travel_Log_Dropdown_Taxonomies_Control( $wp_customize, 'travel_log_options[home_slider_category_trip_type]',
		array(
			'label'       => esc_html__( 'Select Trip Type', 'travel-log' ),
			'description' => esc_html__( 'Select the desired Trip Type for Slider section', 'travel-log' ),
			'section'     => 'travel_log_homepage_slider_section',
			'settings'    => 'travel_log_options[home_slider_category_trip_type]',
			'priority'    => 22,
			'multiple'    => true,
			'active_callback' => 'travel_log_slider_is_trip_type_selected',
			'taxonomy' => 'itinerary_types',
		)
	)
);

// Category setting.
$wp_customize->add_setting('travel_log_options[home_slider_category_trip_location]', array(
	'default' => $defaults['home_slider_category_trip_location'],
	'sanitize_callback' => 'travel_log_sanitize_multiple_dropdown_taxonomies',
));

// Category control.
$wp_customize->add_control(
	new Travel_Log_Dropdown_Taxonomies_Control( $wp_customize, 'travel_log_options[home_slider_category_trip_location]',
		array(
			'label'       => esc_html__( 'Select Trip Location', 'travel-log' ),
			'description' => esc_html__( 'Select the desired Trip Location for Slider section', 'travel-log' ),
			'section'     => 'travel_log_homepage_slider_section',
			'settings'    => 'travel_log_options[home_slider_category_trip_location]',
			'priority'    => 22,
			'multiple'    => true,
			'active_callback' => 'travel_log_is_slider_trip_location_selected',
			'taxonomy' => 'travel_locations',
		)
	)
);

// Category partial refresh.
$wp_customize->selective_refresh->add_partial( 'travel_log_options[home_slider_category]', array(
	'selector' => '#travel-log-front-page-slider-wrap',
	'render_callback' => 'travel_log_front_page_slider_content',
) );

// Slider speed setting.
$wp_customize->add_setting( 'travel_log_options[home_slider_speed]', array(
	'default' => $defaults['home_slider_speed'],
	'sanitize_callback' => 'travel_log_sanitize_integer',
) );

// Slider speed control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_slider_speed', array(
	'label'    => esc_html__( 'Slider Speed', 'travel-log' ),
	'description' => esc_html__( 'Enter number in milliseconds.', 'travel-log' ),
	'section'  => 'travel_log_homepage_slider_section',
	'settings' => 'travel_log_options[home_slider_speed]',
	'priority'    => 23,
) ) );

// Read More Button Text setting.
$wp_customize->add_setting( 'travel_log_options[home_slider_read_more_text]', array(
	'default' => $defaults['home_slider_read_more_text'],
	'sanitize_callback' => 'sanitize_text_field',
) );

// Read More Button Text control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_slider_read_more_text', array(
	'label'    => esc_html__( 'Read More Button Text', 'travel-log' ),
	'description' => esc_html__( 'Enter the text for Slider read more button.Leave Blank to disable the button', 'travel-log' ),
	'section'  => 'travel_log_homepage_slider_section',
	'settings' => 'travel_log_options[home_slider_read_more_text]',
	'priority'    => 24,
) ) );

// Enable Book Now Button.
$wp_customize->add_setting('travel_log_options[home_slider_book_now_enable]', array(
	'default'        => $defaults['home_slider_book_now_enable'],
	'sanitize_callback' => 'travel_log_sanitize_checkbox',
));

// enable field control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_options[home_slider_book_now_enable]', array(
	'label'    => esc_html__( 'Enable Book Now button ?', 'travel-log' ),
	'description' => esc_html__( 'Check to enable additional tour Book Now button in slider', 'travel-log' ),
	'section'  => 'travel_log_homepage_slider_section',
	'settings' => 'travel_log_options[home_slider_book_now_enable]',
	'type'     => 'checkbox',
	'priority'    => 24,
	'active_callback' => 'travel_log_is_slider_itinerary_posts_selected',
) ) );

// Section: Itinerary Search Slider.
$wp_customize->add_section( 'travel_log_homepage_itinerary_search_section' , array(
	'title'       => esc_html__( 'Itinerary Search Section', 'travel-log' ),
	'priority'    => 20,
	'panel'  => 'travel_log_homepage_panel',
) );

// Diable field setting.
$wp_customize->add_setting('travel_log_options[home_itinerary_search_enable]', array(
	'default'        => $defaults['home_itinerary_search_enable'],
	'sanitize_callback' => 'travel_log_sanitize_checkbox',
));

// Title setting.
$wp_customize->add_setting('travel_log_options[itinerary_search_title]', array(
	'default' => $defaults['itinerary_search_title'],
	'sanitize_callback' => 'sanitize_text_field',
));

// Title control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_itinerary_search_title', array(
	'type' => 'text',
	'label' => esc_html__( 'Title', 'travel-log' ),
	'description' => esc_html__( 'Title for the itinerary search section. Leave blank to remove title.', 'travel-log' ),
	'section'  => 'travel_log_homepage_itinerary_search_section',
	'settings' => 'travel_log_options[itinerary_search_title]',
	'priority'    => 22,
	'active_callback' => 'travel_log_callback_is_itinerary_search_enable',
)));

// Title partial refresh.
$wp_customize->selective_refresh->add_partial( 'travel_log_options[itinerary_search_title]', array(
	'selector' => '#travel-log-front-page-itinerary-search-wrap',
	'render_callback' => 'travel_log_front_page_itinerary_search_wrap',
) );

// Sub title setting.
$wp_customize->add_setting('travel_log_options[itinerary_search_sub_title]', array(
	'default' => $defaults['itinerary_search_sub_title'],
	'sanitize_callback' => 'sanitize_text_field',
));

// Sub title control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_itinerary_search_sub_title', array(
	'type' => 'text',
	'label' => esc_html__( 'Sub Title', 'travel-log' ),
	'description' => esc_html__( 'Subtitle for the itinerary search section. Leave blank to remove subtitle.', 'travel-log' ),
	'section'  => 'travel_log_homepage_itinerary_search_section',
	'settings' => 'travel_log_options[itinerary_search_sub_title]',
	'priority'    => 22,
	'active_callback' => 'travel_log_callback_is_itinerary_search_enable',
)));

// enable field control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_options[home_itinerary_search_enable]', array(
	'label'    => esc_html__( 'Enable Section', 'travel-log' ),
	'description' => esc_html__( 'Check to enable Itinerary Search section with multiple filters below frontpage slider', 'travel-log' ),
	'section'  => 'travel_log_homepage_itinerary_search_section',
	'settings' => 'travel_log_options[home_itinerary_search_enable]',
	'type'     => 'checkbox',
	'priority'    => 21,
	'active_callback' => 'travel_log_is_wp_travel_plugin_active',
) ) );

// Section: Post filter.
$wp_customize->add_section( 'travel_log_homepage_post_filter_section' , array(
	'title'       => esc_html__( 'Post Filter Section', 'travel-log' ),
	'priority'    => 20,
	'panel'  => 'travel_log_homepage_panel',
) );

// Diable field setting.
$wp_customize->add_setting('travel_log_options[post_filter_enable]', array(
	'default'        => $defaults['post_filter_enable'],
	'sanitize_callback' => 'travel_log_sanitize_checkbox',
));

// Diable field control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_post_filter_enable', array(
	'label'    => esc_html__( 'Enable Section', 'travel-log' ),
	'section'  => 'travel_log_homepage_post_filter_section',
	'settings' => 'travel_log_options[post_filter_enable]',
	'type'     => 'checkbox',
	'priority'    => 21,
) ) );

// Title setting.
$wp_customize->add_setting('travel_log_options[post_filter_title]', array(
	'default' => $defaults['post_filter_title'],
	'sanitize_callback' => 'sanitize_text_field',
));

// Title control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_post_filter_title', array(
	'type' => 'text',
	'label' => esc_html__( 'Title', 'travel-log' ),
	'section'  => 'travel_log_homepage_post_filter_section',
	'settings' => 'travel_log_options[post_filter_title]',
	'priority'    => 22,
)));

// Title partial refresh.
$wp_customize->selective_refresh->add_partial( 'travel_log_options[post_filter_title]', array(
	'selector' => '#travel-log-front-page-post-filter-wrap',
	'render_callback' => 'travel_log_front_page_post_filter_content',
) );

// Sub title setting.
$wp_customize->add_setting('travel_log_options[post_filter_sub_title]', array(
	'default' => $defaults['post_filter_sub_title'],
	'sanitize_callback' => 'sanitize_text_field',
));

// Sub title control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_post_filter_sub_title', array(
	'type' => 'text',
	'label' => esc_html__( 'Sub Title', 'travel-log' ),
	'section'  => 'travel_log_homepage_post_filter_section',
	'settings' => 'travel_log_options[post_filter_sub_title]',
	'priority'    => 22,
)));

// Add Trip Filter Content Type Setting / Control.
$wp_customize->add_setting( 'travel_log_options[post_filter_content_type]', array(
	'default'           => $defaults['post_filter_content_type'],
	'sanitize_callback' => 'travel_log_sanitize_choices',
) );

$wp_customize->add_control( 'travel_log_options[post_filter_content_type]', array(
	'label'           	=> esc_html__( 'Content Type', 'travel-log' ),
	'section'         	=> 'travel_log_homepage_post_filter_section',
	'type'            	=> 'select',
	'priority'   		=> 23,
	'choices'         	=> travel_log_customizer_content_type_choices(),
) );

// Category setting.
$wp_customize->add_setting('travel_log_options[post_filter_category]', array(
	'default' => $defaults['post_filter_category'],
	'sanitize_callback' => 'travel_log_sanitize_multiple_dropdown_taxonomies',
));

// Category control.
$wp_customize->add_control(
	new Travel_Log_Dropdown_Taxonomies_Control( $wp_customize, 'travel_log_options[post_filter_category]',
		array(
			'label'       => esc_html__( 'Select Category', 'travel-log' ),
			'description' => esc_html__( 'Hold down the Ctrl (windows) / Command (Mac) button to select multiple options.', 'travel-log' ),
			'section'     => 'travel_log_homepage_post_filter_section',
			'settings'    => 'travel_log_options[post_filter_category]',
			'priority'    => 100,
			'multiple'    => true,
			'active_callback' => 'travel_log_is_post_category_selected',
		)
	)
);

// Category setting.
$wp_customize->add_setting('travel_log_options[post_filter_category_trip_type]', array(
	'default' => $defaults['post_filter_category_trip_type'],
	'sanitize_callback' => 'travel_log_sanitize_multiple_dropdown_taxonomies',
));

// Category control.
$wp_customize->add_control(
	new Travel_Log_Dropdown_Taxonomies_Control( $wp_customize, 'travel_log_options[post_filter_category_trip_type]',
		array(
			'label'       => esc_html__( 'Select Trip Types', 'travel-log' ),
			'description' => esc_html__( 'Hold down the Ctrl (windows) / Command (Mac) button to select multiple options.', 'travel-log' ),
			'section'     => 'travel_log_homepage_post_filter_section',
			'settings'    => 'travel_log_options[post_filter_category_trip_type]',
			'priority'    => 100,
			'multiple'    => true,
			'active_callback' => 'travel_log_is_trip_type_selected',
			'taxonomy' => 'itinerary_types',
		)
	)
);

// Category setting.
$wp_customize->add_setting('travel_log_options[post_filter_category_trip_location]', array(
	'default' => $defaults['post_filter_category_trip_location'],
	'sanitize_callback' => 'travel_log_sanitize_multiple_dropdown_taxonomies',
));

// Category control.
$wp_customize->add_control(
	new Travel_Log_Dropdown_Taxonomies_Control( $wp_customize, 'travel_log_options[post_filter_category_trip_location]',
		array(
			'label'       => esc_html__( 'Select Trip Locations', 'travel-log' ),
			'description' => esc_html__( 'Hold down the Ctrl (windows) / Command (Mac) button to select multiple options.', 'travel-log' ),
			'section'     => 'travel_log_homepage_post_filter_section',
			'settings'    => 'travel_log_options[post_filter_category_trip_location]',
			'priority'    => 100,
			'multiple'    => true,
			'active_callback' => 'travel_log_is_trip_location_selected',
			'taxonomy' => 'travel_locations',
		)
	)
);

// Section: Call to action.
$wp_customize->add_section( 'travel_log_homepage_call_to_action_section' , array(
	'title'       => esc_html__( 'Call To Action Section', 'travel-log' ),
	'priority'    => 20,
	'panel'  => 'travel_log_homepage_panel',
) );

// Diable field setting.
$wp_customize->add_setting('travel_log_options[call_to_action_enable]', array(
	'default'        => $defaults['call_to_action_enable'],
	'sanitize_callback' => 'travel_log_sanitize_checkbox',
));

// Diable field control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_call_to_action_enable', array(
	'label'    => esc_html__( 'Enable Section', 'travel-log' ),
	'section'  => 'travel_log_homepage_call_to_action_section',
	'settings' => 'travel_log_options[call_to_action_enable]',
	'type'     => 'checkbox',
	'priority'    => 21,
) ) );

// CTA Content setting.
$wp_customize->add_setting('travel_log_options[call_to_action_content_page]', array(
	'default'        => $defaults['call_to_action_content_page'],
	'sanitize_callback' => 'absint',
));

// CTA Content control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_call_to_action_content_page', array(
	'label'    => esc_html__( 'Call to Action Content', 'travel-log' ),
	'description' => esc_html__( 'choose page to display contents in Call to Action section,The featured image of the selected page will be used as the background image', 'travel-log' ),
	'section'  => 'travel_log_homepage_call_to_action_section',
	'settings' => 'travel_log_options[call_to_action_content_page]',
	'type'     => 'dropdown-pages',
	'priority'    => 21,
) ) );

// CTA excerpt length setting.
$wp_customize->add_setting('travel_log_options[call_to_action_excerpt_length]', array(
	'default'        => $defaults['call_to_action_excerpt_length'],
	'sanitize_callback' => 'absint',
));

// CTA excerpt length control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_call_to_action_excerpt_length', array(
	'label'    => esc_html__( 'Call to Action Excerpt Length', 'travel-log' ),
	'description' => esc_html__( 'No. of words for the Call To Action Sub-title', 'travel-log' ),
	'section'  => 'travel_log_homepage_call_to_action_section',
	'settings' => 'travel_log_options[call_to_action_excerpt_length]',
	'type'     => 'text',
	'priority'    => 22,
) ) );

// CTA partial refresh.
$wp_customize->selective_refresh->add_partial( 'travel_log_options[call_to_action_content_page]', array(
	'selector' => '#travel-log-front-page-call-to-action-wrap',
	'render_callback' => 'travel_log_front_page_call_to_action_content',
) );

// Button text setting.
$wp_customize->add_setting('travel_log_options[call_to_action_button_text]', array(
	'default' => $defaults['call_to_action_button_text'],
	'sanitize_callback' => 'sanitize_text_field',
));

// Button text control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_call_to_action_button_text', array(
	'type' => 'text',
	'label' => esc_html__( 'Button Text', 'travel-log' ),
	'section'  => 'travel_log_homepage_call_to_action_section',
	'settings' => 'travel_log_options[call_to_action_button_text]',
	'priority'    => 22,
)));

// CTA Button partial refresh.
$wp_customize->selective_refresh->add_partial( 'travel_log_options[call_to_action_button_text]', array(
	'selector' => '#cta-button',
	'render_callback' => 'travel_log_front_page_call_to_action_content',
) );

// Section: Recommended Trips.
$wp_customize->add_section( 'travel_log_homepage_recommended_section' , array(
	'title'       => esc_html__( 'Recommended Posts Section', 'travel-log' ),
	'priority'    => 20,
	'panel'  => 'travel_log_homepage_panel',
) );

// enable field setting.
$wp_customize->add_setting('travel_log_options[recommended_posts_enable]', array(
	'default' => $defaults['recommended_posts_enable'],
	'sanitize_callback' => 'travel_log_sanitize_checkbox',
));

// enable field control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_options[recommended_posts_enable]', array(
	'label'    => esc_html__( 'Enable Section', 'travel-log' ),
	'section'  => 'travel_log_homepage_recommended_section',
	'settings' => 'travel_log_options[recommended_posts_enable]',
	'type'     => 'checkbox',
	'priority'    => 21,
) ) );

// Title setting.
$wp_customize->add_setting('travel_log_options[recommended_posts_title]', array(
	'default' => $defaults['recommended_posts_title'],
	'sanitize_callback' => 'sanitize_text_field',
));

// Title control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_options[recommended_posts_title]', array(
	'type' => 'text',
	'label' => esc_html__( 'Title', 'travel-log' ),
	'section'  => 'travel_log_homepage_recommended_section',
	'settings' => 'travel_log_options[recommended_posts_title]',
	'priority'    => 22,
)));

// Title partial refresh.
$wp_customize->selective_refresh->add_partial( 'travel_log_options[recommended_posts_title]', array(
	'selector' => '#travel-log-front-page-recommended-posts-wrap',
	'render_callback' => 'travel_log_front_page_recommended_content',
) );

// Sub Title setting.
$wp_customize->add_setting('travel_log_options[recommended_posts_sub_title]', array(
	'default' => $defaults['recommended_posts_sub_title'],
	'sanitize_callback' => 'sanitize_text_field',
));

// Sub Title control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_recommended_sub_title', array(
	'type' => 'text',
	'label' => esc_html__( 'Sub Title', 'travel-log' ),
	'section'  => 'travel_log_homepage_recommended_section',
	'settings' => 'travel_log_options[recommended_posts_sub_title]',
	'priority'    => 22,
)));

// Add Trip Filter Content Type Setting / Control.
$wp_customize->add_setting( 'travel_log_options[home_recommended_content_type]', array(
	'default'           => $defaults['home_recommended_content_type'],
	'sanitize_callback' => 'travel_log_sanitize_choices',
) );

$wp_customize->add_control( 'travel_log_options[home_recommended_content_type]', array(
	'label'           	=> esc_html__( 'Content Type', 'travel-log' ),
	'section'         	=> 'travel_log_homepage_recommended_section',
	'type'            	=> 'select',
	'priority'   		=> 23,
	'choices'         	=> travel_log_customizer_recommended_content_type_choices(),
) );

// Category setting.
$wp_customize->add_setting('travel_log_options[recommended_posts_category]', array(
	'default' => $defaults['recommended_posts_category'],
	'sanitize_callback' => 'travel_log_sanitize_category',
));

// Category control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_category_recommended', array(
	'type' => 'select',
	'label' => esc_html__( 'Select Category', 'travel-log' ),
	'section'  => 'travel_log_homepage_recommended_section',
	'settings' => 'travel_log_options[recommended_posts_category]',
	'choices' => travel_log_list_category(),
	'priority'    => 23,
	'active_callback' => 'travel_log_is_recommended_post_category_selected',
)));

// Section: Testimonials.
$wp_customize->add_section( 'travel_log_homepage_testimonials_section' , array(
	'title'       => esc_html__( 'Testimonials Section', 'travel-log' ),
	'priority'    => 20,
	'panel'  => 'travel_log_homepage_panel',
) );

// Diable field setting.
$wp_customize->add_setting('travel_log_options[testimonials_enable]', array(
	'default'        => $defaults['testimonials_enable'],
	'sanitize_callback' => 'travel_log_sanitize_checkbox',
));

// enable field control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_testimonials_enable', array(
	'label'    => esc_html__( 'Enable Section', 'travel-log' ),
	'section'  => 'travel_log_homepage_testimonials_section',
	'settings' => 'travel_log_options[testimonials_enable]',
	'type'     => 'checkbox',
	'priority'    => 21,
) ) );

// Title setting.
$wp_customize->add_setting('travel_log_options[testimonials_title]', array(
	'default' => $defaults['testimonials_title'],
	'sanitize_callback' => 'sanitize_text_field',
));

// Title control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_testimonials_title', array(
	'type' => 'text',
	'label' => esc_html__( 'Title', 'travel-log' ),
	'section'  => 'travel_log_homepage_testimonials_section',
	'settings' => 'travel_log_options[testimonials_title]',
	'priority'    => 22,
)));

// Category partial refresh.
$wp_customize->selective_refresh->add_partial( 'travel_log_options[testimonials_title]', array(
	'selector' => '#travel-log-front-page-testimonials-wrap',
	'render_callback' => 'travel_log_front_page_testimonials_content',
) );

// Add Testimonials Content Type Setting / Control.
$wp_customize->add_setting( 'travel_log_options[home_testimonials_content_type]', array(
	'default'           => $defaults['home_testimonials_content_type'],
	'sanitize_callback' => 'travel_log_sanitize_choices',
) );

$wp_customize->add_control( 'travel_log_options[home_testimonials_content_type]', array(
	'label'           	=> esc_html__( 'Content Type', 'travel-log' ),
	'section'         	=> 'travel_log_homepage_testimonials_section',
	'type'            	=> 'select',
	'priority'   		=> 22,
	'choices'         	=> travel_log_customizer_testimonials_secttion_content_type_choices(),
) );

// Category setting.
$wp_customize->add_setting('travel_log_options[testimonials_category]', array(
	'default' => $defaults['testimonials_category'],
	'sanitize_callback' => 'travel_log_sanitize_category',
));

// Category control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_category_testimonials', array(
	'type' => 'select',
	'label' => esc_html__( 'Select Category', 'travel-log' ),
	'section'  => 'travel_log_homepage_testimonials_section',
	'settings' => 'travel_log_options[testimonials_category]',
	'choices' => travel_log_list_category(),
	'priority'    => 22,
	'active_callback' => 'travel_log_is_testimonial_post_selected',
)));

// Background URL setting.
$wp_customize->add_setting('travel_log_options[testimonials_bg_url]', array(
	'default' => $defaults['testimonials_bg_url'],
	'sanitize_callback' => 'esc_url_raw',
));

// Background Image control.
$wp_customize->add_control(
	new WP_Customize_Image_Control(
		$wp_customize,
		'travel_log_homepage_testimonials_bg_url',
		array(
			   'label'      => esc_html__( 'Background Image', 'travel-log' ),
			   'description' => esc_html__( 'Upload the image for Testimonials section background', 'travel-log' ),
			   'section'    => 'travel_log_homepage_testimonials_section',
			   'settings'   => 'travel_log_options[testimonials_bg_url]',
			   'priority'    => 22,
		   )
	)
);

// Slider speed setting.
$wp_customize->add_setting( 'travel_log_options[testimonials_speed]', array(
	'default' => $defaults['testimonials_speed'],
	'sanitize_callback' => 'travel_log_sanitize_integer',
) );

// Slider speed control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_testimonials_speed', array(
	'label'    => esc_html__( 'Slider Speed', 'travel-log' ),
	'description' => esc_html__( 'Enter number in milliseconds.', 'travel-log' ),
	'section'  => 'travel_log_homepage_testimonials_section',
	'settings' => 'travel_log_options[testimonials_speed]',
	'priority'    => 23,
) ) );

// Section: Latest Blog.
$wp_customize->add_section( 'travel_log_homepage_latest_post_section' , array(
	'title'       => esc_html__( 'Latest Posts Section', 'travel-log' ),
	'priority'    => 20,
	'panel'  => 'travel_log_homepage_panel',
) );

// enable field setting.
$wp_customize->add_setting('travel_log_options[latest_posts_enable]', array(
	'default'        => $defaults['latest_posts_enable'],
	'sanitize_callback' => 'travel_log_sanitize_checkbox',
));

// Disble field control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_latest_post_enable', array(
	'label'    => esc_html__( 'Enable Section', 'travel-log' ),
	'section'  => 'travel_log_homepage_latest_post_section',
	'settings' => 'travel_log_options[latest_posts_enable]',
	'type'     => 'checkbox',
	'priority'    => 21,
) ) );

// Title setting.
$wp_customize->add_setting('travel_log_options[latest_posts_title]', array(
	'default' => $defaults['latest_posts_title'],
	'sanitize_callback' => 'sanitize_text_field',
));

// Title control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_latest_post_title', array(
	'type' => 'text',
	'label' => esc_html__( 'Title', 'travel-log' ),
	'section'  => 'travel_log_homepage_latest_post_section',
	'settings' => 'travel_log_options[latest_posts_title]',
	'priority'    => 22,
)));

// Title partial refresh.
$wp_customize->selective_refresh->add_partial( 'travel_log_options[latest_posts_title]', array(
	'selector' => '#travel-log-front-page-latest-posts-wrap',
	'render_callback' => 'travel_log_front_page_latest_post_content',
) );

// Sub Title setting.
$wp_customize->add_setting('travel_log_options[latest_posts_sub_title]', array(
	'default' => $defaults['latest_posts_sub_title'],
	'sanitize_callback' => 'sanitize_text_field',
));

// Sub title control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_options[latest_posts_sub_title]', array(
	'type' => 'text',
	'label' => esc_html__( 'Sub Title', 'travel-log' ),
	'section'  => 'travel_log_homepage_latest_post_section',
	'settings' => 'travel_log_options[latest_posts_sub_title]',
	'priority'    => 22,
)));

// Category Exclude setting.
$wp_customize->add_setting('travel_log_options[latest_posts_excluded_cats]', array(
	'default' => $defaults['latest_posts_excluded_cats'],
	'sanitize_callback' => 'sanitize_text_field',
));

// Category Exclude control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_latest_post_sub_title', array(
	'type' => 'text',
	'label' => esc_html__( 'Exclude Categories', 'travel-log' ),
	'description' => esc_html__( 'Enter Comma Separated Category IDs to exclude from Latest Posts Listing', 'travel-log' ),
	'section'  => 'travel_log_homepage_latest_post_section',
	'settings' => 'travel_log_options[latest_posts_excluded_cats]',
	'priority'    => 22,
)));

// Slider speed setting.
$wp_customize->add_setting( 'travel_log_options[latest_posts_speed]', array(
	'default' => $defaults['latest_posts_speed'],
	'sanitize_callback' => 'travel_log_sanitize_integer',
) );

// Slider speed control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_latest_post_speed', array(
	'label'    => esc_html__( 'Slider Speed', 'travel-log' ),
	'description' => esc_html__( 'Enter number in milliseconds.', 'travel-log' ),
	'section'  => 'travel_log_homepage_latest_post_section',
	'settings' => 'travel_log_options[latest_posts_speed]',
	'priority'    => 23,
) ) );
