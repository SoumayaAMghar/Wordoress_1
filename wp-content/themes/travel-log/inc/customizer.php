<?php
/**
 * Travel Log Theme Customizer
 *
 * @package Travel_Log
 */

	/**
	 * List category.
	 *
	 * @param  boolean $add_none To add empty value or not.
	 * @return array
	 */
function travel_log_list_category( $add_none = false ) {
	$categories = get_categories();
	$cats = array(
	'' => esc_html__( 'None', 'travel-log' ),
	);
	if ( true !== $add_none ) {
		$cats = array();
	}
	$i = 0;
	foreach ( $categories as $category ) {
		if ( 0 === $i ) {
			$default = $category->slug;
			$i++;
		}
		$cats[ $category->slug ] = $category->name;
	}

	return $cats;
}

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function travel_log_customize_register( $wp_customize ) {

	$defaults = travel_log_default_values();
	/**
	 * Controls
	 */
	require get_template_directory() . '/inc/controls.php';

	/**
	 * Front page Customizer Options Hook.
	 */
	$front_section_options = apply_filters( 'travel_log_home_page_sections_customizer_options', true );

	if ( true === $front_section_options ) {
		// Home page customizer.
		require get_template_directory() . '/inc/customizer/front-page.php';
	}
	// Color Options.
	require get_template_directory() . '/inc/customizer/colors.php';

	// Footer Options.
	require get_template_directory() . '/inc/customizer/footer-options.php';

	// Theme Options.
	require get_template_directory() . '/inc/customizer/theme-options.php';

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->default = $defaults['header_text_color'];
	$wp_customize->get_setting( 'background_color' )->default = $defaults['background_color'];

	$wp_customize->selective_refresh->add_partial( 'blogname', array(
		'selector' => '.site-title a',
		'render_callback' => 'travel_log_customize_partial_blogname',
	) );

	$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
		'selector' => '.site-description',
		'render_callback' => 'travel_log_customize_partial_blogdescription',
	) );

	/**
	* Layout Options
	*/
	$wp_customize->add_section( 'travel_log_options[travel_log_layout]' , array(
		'title'      			=> esc_html__( 'Layout', 'travel-log' ),
		'priority'   			=> 50,
	) );

	$wp_customize->add_setting( 'travel_log_options[travel_log_layout]', array(
		'default'    			=> $defaults['travel_log_layout'],
		'sanitize_callback' 	=> 'travel_log_sanitize_choices',
	) );

	$wp_customize->add_control( new Travel_Log_Custom_Radio_Image_Control( $wp_customize, 'travel_log_options[travel_log_layout]', array(
		'settings'				=> 'travel_log_options[travel_log_layout]',
		'section'				=> 'travel_log_options[travel_log_layout]',
		'label'					=> esc_html__( 'General Layout', 'travel-log' ),
		'priority'				=> 1,
		'choices'				=> array(
									'full' => array(
										'label' => esc_html__( 'Full width', 'travel-log' ),
										'image' => get_template_directory_uri() . '/images/customizer/controls/2cf.png',
									),
									'right' => array(
										'label' => esc_html__( 'Right sidebar', 'travel-log' ),
										'image' => get_template_directory_uri() . '/images/customizer/controls/2cr.png',
									),
									'left' => array(
										'label' => esc_html__( 'Left sidebar', 'travel-log' ),
										'image' => get_template_directory_uri() . '/images/customizer/controls/2cl.png',
									),
		),
	) ) );

	// Static Front Page Content Option.
	// enable field setting.
		$wp_customize->add_setting('travel_log_options[enable_home_content]', array(
			'default' => $defaults['enable_home_content'],
			'sanitize_callback' => 'travel_log_sanitize_checkbox',
			'active_callback' => 'travel_log_has_front_page',
		));

		// enable field control.
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'travel_log_homepage_recommended_enable', array(
			'label'    => esc_html__( 'Enable Static Front Page Contents', 'travel-log' ),
			'description' => esc_html__( 'Check to enable static front page content block below Homepage Sections.', 'travel-log' ),
			'section'  => 'static_front_page',
			'settings' => 'travel_log_options[enable_home_content]',
			'type'     => 'checkbox',
			'priority'    => 21,
		) ) );
}
add_action( 'customize_register', 'travel_log_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function travel_log_customize_preview_js() {
	wp_enqueue_script( 'travel_log_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'travel_log_customize_preview_js' );

/**
 * [travel_log_customize_backend_scripts Add Customizer Style / Scripts]
 *
 * @since 1.0.0
 */
function travel_log_customize_backend_scripts() {

	wp_enqueue_style( 'travel-log-admin-customizer-style', get_template_directory_uri() . '/inc/customizer/css/customizer-style.css' );
	wp_enqueue_script( 'travel-log-admin-customizer', get_template_directory_uri() . '/inc/customizer/js/customizer-script.js', array(), '20151215', true );
}
add_action( 'customize_controls_enqueue_scripts', 'travel_log_customize_backend_scripts', 10 );
