<?php
/**
 * Color Options
 *
 * @package Travel_Log
 */

// Link Color setting.
$wp_customize->add_setting('travel_log_options[travel_log_link_color]', array(
	'default'           => $defaults['travel_log_link_color'],
	'sanitize_callback' => 'sanitize_hex_color',
));

// Link Color Control.
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'travel_log_options[travel_log_link_color]', array(
	'label'       => esc_html__( 'Links Color', 'travel-log' ),
	'description' => esc_html__( 'Color for links', 'travel-log' ),
	'section'     => 'colors',
	'settings'    => 'travel_log_options[travel_log_link_color]',
	'priority'    => 21,
) ) );

// Link Hover Color setting.
$wp_customize->add_setting('travel_log_options[travel_log_link_hover_color]', array(
	'default'           => $defaults['travel_log_link_hover_color'],
	'sanitize_callback' => 'sanitize_hex_color',
));

// Link Hover Color Control.
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'travel_log_options[travel_log_link_hover_color]', array(
	'label'       => esc_html__( 'Links Hover Color', 'travel-log' ),
	'description' => esc_html__( 'Color for links on hover', 'travel-log' ),
	'section'     => 'colors',
	'settings'    => 'travel_log_options[travel_log_link_hover_color]',
	'priority'    => 21,
) ) );

// Button background Color setting.
$wp_customize->add_setting('travel_log_options[travel_log_button_color]', array(
	'default'           => $defaults['travel_log_button_color'],
	'sanitize_callback' => 'sanitize_hex_color',
));

// Button Color Control.
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'travel_log_options[travel_log_button_color]', array(
	'label'       => esc_html__( 'Button Background Color', 'travel-log' ),
	'description' => esc_html__( 'Color for button background', 'travel-log' ),
	'section'     => 'colors',
	'settings'    => 'travel_log_options[travel_log_button_color]',
	'priority'    => 21,
) ) );

// Button Hover Color setting.
$wp_customize->add_setting('travel_log_options[travel_log_button_hover_color]', array(
	'default'           => $defaults['travel_log_button_hover_color'],
	'sanitize_callback' => 'sanitize_hex_color',
));

// Button Hover Color Control.
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'travel_log_options[travel_log_button_hover_color]', array(
	'label'       => esc_html__( 'Button Background Hover Color', 'travel-log' ),
	'description' => esc_html__( 'Color for buttons on hover', 'travel-log' ),
	'section'     => 'colors',
	'settings'    => 'travel_log_options[travel_log_button_hover_color]',
	'priority'    => 21,
) ) );

// Button Text Color setting.
$wp_customize->add_setting('travel_log_options[travel_log_button_text_color]', array(
	'default'           => $defaults['travel_log_button_text_color'],
	'sanitize_callback' => 'sanitize_hex_color',
));

// Button text Color Control.
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'travel_log_options[travel_log_button_text_color]', array(
	'label'       => esc_html__( 'Button Text Color', 'travel-log' ),
	'description' => esc_html__( 'Color for button text', 'travel-log' ),
	'section'     => 'colors',
	'settings'    => 'travel_log_options[travel_log_button_text_color]',
	'priority'    => 21,
) ) );

// Button Text hover Color setting.
$wp_customize->add_setting('travel_log_options[travel_log_button_text_hover_color]', array(
	'default'           => $defaults['travel_log_button_text_hover_color'],
	'sanitize_callback' => 'sanitize_hex_color',
));

// Button text hover Color Control.
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'travel_log_options[travel_log_button_text_hover_color]', array(
	'label'       => esc_html__( 'Button Text Hover Color', 'travel-log' ),
	'description' => esc_html__( 'Color for button text', 'travel-log' ),
	'section'     => 'colors',
	'settings'    => 'travel_log_options[travel_log_button_text_hover_color]',
	'priority'    => 21,
) ) );

// Button Text hover Color setting.
$wp_customize->add_setting('travel_log_options[travel_log_footer_bg_color]', array(
	'default'           => $defaults['travel_log_footer_bg_color'],
	'sanitize_callback' => 'sanitize_hex_color',
));

// Button text hover Color Control.
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'travel_log_options[travel_log_footer_bg_color]', array(
	'label'       => esc_html__( 'Footer Section Background Color', 'travel-log' ),
	'description' => esc_html__( 'Color for button text', 'travel-log' ),
	'section'     => 'colors',
	'settings'    => 'travel_log_options[travel_log_footer_bg_color]',
	'priority'    => 21,
) ) );
