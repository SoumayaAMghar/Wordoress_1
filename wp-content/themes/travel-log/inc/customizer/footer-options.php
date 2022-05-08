<?php
/**
 * Front page sections.
 *
 * @package Travel_Log
 */

// Home page panel.
$wp_customize->add_panel( 'travel_log_footer_options_panel', array(
	'priority'       => 30,
	'capability'     => 'edit_theme_options',
	'theme_supports' => '',
	'title'          => esc_html__( 'Footer Options', 'travel-log' ),
	'description'    => '',
) );

// Section: Footer Menu Options.
$wp_customize->add_section( 'travel_log_footer_menu_section' , array(
	'title'       => esc_html__( 'Footer Menu ', 'travel-log' ),
	'priority'    => 20,
	'panel'  => 'travel_log_footer_options_panel',
) );

// Diable fotter menu setting.
$wp_customize->add_setting('travel_log_options[footer_menu_enable]', array(
	'default'        => $defaults['footer_menu_enable'],
	'sanitize_callback' => 'travel_log_sanitize_checkbox',
));

// enable footer menu control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_options[footer_menu_enable]', array(
	'label'    => esc_html__( 'Enable Menu Display in Footer', 'travel-log' ),
	'description'    => esc_html__( 'Check to enable menu in footer', 'travel-log' ),
	'section'  => 'travel_log_footer_menu_section',
	'settings' => 'travel_log_options[footer_menu_enable]',
	'type'     => 'checkbox',
	'priority'    => 21,
) ) );

// Section: Footer Copyright section.
$wp_customize->add_section( 'travel_log_footer_copyright_section' , array(
	'title'       => esc_html__( 'Footer Copyright ', 'travel-log' ),
	'priority'    => 20,
	'panel'  => 'travel_log_footer_options_panel',
) );

// Diable Copyright setting.
$wp_customize->add_setting('travel_log_options[footer_copyright_txt]', array(
	'default'        => $defaults['footer_copyright_txt'],
	'sanitize_callback' => 'wp_kses_post',
));

// Disable Copyright control.
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_options[footer_copyright_txt]', array(
	'label'    => esc_html__( 'Footer Copyright Text', 'travel-log' ),
	'description'    => esc_html__( 'Enter the copyright text to display in footer', 'travel-log' ),
	'section'  => 'travel_log_footer_copyright_section',
	'settings' => 'travel_log_options[footer_copyright_txt]',
	'type'     => 'textarea',
	'priority'    => 21,
) ) );
