<?php
/**
 * Customizer settings for theme options
 *
 * @package Travel_Log
 *
 * @since 1.0.0
 */

// Theme Options panel.
$wp_customize->add_panel( 'travel_log_theme_options_panel', array(
	'priority'       => 25,
	'capability'     => 'edit_theme_options',
	'theme_supports' => '',
	'title'          => esc_html__( 'Theme Options', 'travel-log' ),
	'description'    => '',
) );

// Section: Breadcrumbs Options.
$wp_customize->add_section( 'travel_log_breadcrumb_options' , array(
	'title'       => esc_html__( 'Breadcrumb Options ', 'travel-log' ),
	'priority'    => 20,
	'panel'  => 'travel_log_theme_options_panel',
) );

// Diable Breadcrumbs setting.
$wp_customize->add_setting('travel_log_options[travel_log_breadcrumb]', array(
	'default'        => $defaults['travel_log_breadcrumb'],
	'sanitize_callback' => 'travel_log_sanitize_checkbox',
));

// Disable Breadcrumbs control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_options[travel_log_breadcrumb]', array(
	'label'    => esc_html__( 'Disable Breadcrumbs Display', 'travel-log' ),
	'description'    => esc_html__( 'Check to disable the breadcrumbs display', 'travel-log' ),
	'section'  => 'travel_log_breadcrumb_options',
	'settings' => 'travel_log_options[travel_log_breadcrumb]',
	'type'     => 'checkbox',
	'priority'    => 21,
) ) );

// Section: Loader Options.
$wp_customize->add_section( 'travel_log_loader_options' , array(
	'title'       => esc_html__( 'Loader Options ', 'travel-log' ),
	'priority'    => 20,
	'panel'  => 'travel_log_theme_options_panel',
) );

// Diable Loader setting.
$wp_customize->add_setting('travel_log_options[travel_log_loader]', array(
	'default'        => $defaults['travel_log_loader'],
	'sanitize_callback' => 'travel_log_sanitize_checkbox',
));

// Disable Loader control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_options[travel_log_loader]', array(
	'label'    => esc_html__( 'Disable Loader', 'travel-log' ),
	'description'    => esc_html__( 'Check to disable the Loader', 'travel-log' ),
	'section'  => 'travel_log_loader_options',
	'settings' => 'travel_log_options[travel_log_loader]',
	'type'     => 'checkbox',
	'priority'    => 21,
) ) );

// Section: Header Options.
$wp_customize->add_section( 'travel_log_header_options' , array(
	'title'       => esc_html__( 'Header Options ', 'travel-log' ),
	'priority'    => 20,
	'panel'  => 'travel_log_theme_options_panel',
) );

// Banner URL setting.
$wp_customize->add_setting('travel_log_options[header_banner_image]', array(
	'default' => $defaults['header_banner_image'],
	'sanitize_callback' => 'esc_url_raw',
));

// Banner Image control.
$wp_customize->add_control(
	new WP_Customize_Image_Control(
		$wp_customize,
		'travel_log_theme_header_banner_image',
		array(
			   'label'      => esc_html__( 'Banner Image', 'travel-log' ),
			   'description' => esc_html__( 'Upload the image for the header banner section', 'travel-log' ),
			   'section'    => 'travel_log_header_options',
			   'settings'   => 'travel_log_options[header_banner_image]',
			   'priority'    => 22,
		   )
	)
);
// Enable WP Travel Search.
$wp_customize->add_setting('travel_log_options[travel_log_enable_header_wp_travel_search]', array(
	'default'        => $defaults['travel_log_enable_header_wp_travel_search'],
	'sanitize_callback' => 'travel_log_sanitize_checkbox',
));

// Disable Sharer control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_options[travel_log_enable_header_wp_travel_search]', 
		array(
		'label'    => esc_html__( 'Enable Itinerary Search in Header Search', 'travel-log' ),
		'description'    => esc_html__( 'Check show Itineraries Search in Header Search', 'travel-log' ),
		'section'  => 'travel_log_header_options',
		'settings' => 'travel_log_options[travel_log_enable_header_wp_travel_search]',
		'type'     => 'checkbox',
		'priority'    => 22,
		'active_callback' => 'travel_log_is_wp_travel_plugin_active',
	) 
));
// Section: Sharer Options.
$wp_customize->add_section( 'travel_log_posts_share_options' , array(
	'title'       => esc_html__( 'Share Options ', 'travel-log' ),
	'priority'    => 20,
	'panel'  => 'travel_log_theme_options_panel',
) );

// Diable Sharer setting.
$wp_customize->add_setting('travel_log_options[travel_log_social_share_disable]', array(
	'default'        => $defaults['travel_log_social_share_disable'],
	'sanitize_callback' => 'travel_log_sanitize_checkbox',
));

// Disable Sharer control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_options[travel_log_social_share_disable]', array(
	'label'    => esc_html__( 'Disable Posts Share', 'travel-log' ),
	'description'    => esc_html__( 'Check to disable the posts share option', 'travel-log' ),
	'section'  => 'travel_log_posts_share_options',
	'settings' => 'travel_log_options[travel_log_social_share_disable]',
	'type'     => 'checkbox',
	'priority'    => 21,
) ) );

// Diable Sharer setting.
$wp_customize->add_setting('travel_log_options[travel_log_social_share_disable]', array(
	'default'        => $defaults['travel_log_social_share_disable'],
	'sanitize_callback' => 'travel_log_sanitize_checkbox',
));

// Disable Sharer control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_options[travel_log_social_share_disable]', array(
	'label'    => esc_html__( 'Disable Posts Share', 'travel-log' ),
	'description'    => esc_html__( 'Check to disable the posts share option', 'travel-log' ),
	'section'  => 'travel_log_posts_share_options',
	'settings' => 'travel_log_options[travel_log_social_share_disable]',
	'type'     => 'checkbox',
	'priority'    => 21,
) ) );

// Enable fb share setting.
$wp_customize->add_setting('travel_log_options[sharer_fb_enable]', array(
	'default'        => $defaults['sharer_fb_enable'],
	'sanitize_callback' => 'travel_log_sanitize_checkbox',

));

// Enable fb share setting. control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_options[sharer_fb_enable]', array(
	'label'    => esc_html__( 'Show Facebook share', 'travel-log' ),
	'description'    => esc_html__( 'Check to enable facebook share in posts Sharer', 'travel-log' ),
	'section'  => 'travel_log_posts_share_options',
	'settings' => 'travel_log_options[sharer_fb_enable]',
	'type'     => 'checkbox',
	'priority'    => 21,
	'active_callback' => 'travel_log_is_social_share_active',
) ) );

// Enable twitter share setting.
$wp_customize->add_setting('travel_log_options[sharer_twitter_enable]', array(
	'default'        => $defaults['sharer_twitter_enable'],
	'sanitize_callback' => 'travel_log_sanitize_checkbox',

));

// Enable twitter share setting. control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_options[sharer_twitter_enable]', array(
	'label'    => esc_html__( 'Show Twitter share', 'travel-log' ),
	'description'    => esc_html__( 'Check to enable Twitter share in posts Sharer', 'travel-log' ),
	'section'  => 'travel_log_posts_share_options',
	'settings' => 'travel_log_options[sharer_twitter_enable]',
	'type'     => 'checkbox',
	'priority'    => 21,
	'active_callback' => 'travel_log_is_social_share_active',
) ) );

// Enable Google Plus share setting.
$wp_customize->add_setting('travel_log_options[sharer_gplus_enable]', array(
	'default'        => $defaults['sharer_gplus_enable'],
	'sanitize_callback' => 'travel_log_sanitize_checkbox',

));

// Enable Google Plus share setting. control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_options[sharer_gplus_enable]', array(
	'label'    => esc_html__( 'Show Google Plus share', 'travel-log' ),
	'description'    => esc_html__( 'Check to enable Google Plus share in posts Sharer', 'travel-log' ),
	'section'  => 'travel_log_posts_share_options',
	'settings' => 'travel_log_options[sharer_gplus_enable]',
	'type'     => 'checkbox',
	'priority'    => 21,
	'active_callback' => 'travel_log_is_social_share_active',
) ) );
