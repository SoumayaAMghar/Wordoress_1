<?php

function travel_log_generate_custom_color_variables( $context = null ) {

	$theme_css        = 'editor' === $context ? ':root .editor-styles-wrapper{' : ':root{';
	$background_color = get_theme_mod( 'background_color', 'f9f9f9' );

	if ( 'f9f9f9' !== strtolower( $background_color ) ) {
		$theme_css .= '--global--color-background: #' . $background_color . ';';
		// $theme_css .= '--global--color-primary: ' . $this->custom_get_readable_color( $background_color ) . ';';
		// $theme_css .= '--global--color-secondary: ' . $this->custom_get_readable_color( $background_color ) . ';';
		// $theme_css .= '--button--color-background: ' . $this->custom_get_readable_color( $background_color ) . ';';
		// $theme_css .= '--button--color-text-hover: ' . $this->custom_get_readable_color( $background_color ) . ';';

		// if ( '#fff' === $this->custom_get_readable_color( $background_color ) ) {
		// 	$theme_css .= '--table--stripes-border-color: rgba(240, 240, 240, 0.15);';
		// 	$theme_css .= '--table--stripes-background-color: rgba(240, 240, 240, 0.15);';
		// }
	}

	$theme_css .= '}';

	return $theme_css;
}

add_action( 'wp_enqueue_scripts', 'travel_log_custom_color_variables' );

function travel_log_custom_color_variables() {
	wp_dequeue_style( 'wp-travel-frontend' );
	wp_enqueue_style( 'wp-travel-frontend-v2' );
	wp_enqueue_style( 'travel-log-roboto-font-css', 'https://fonts.googleapis.com/css?family=Roboto:400,500,700' );
	wp_enqueue_style( 'travel-log-style',  get_template_directory_uri() . '/css/front-block-style.css', array( 'travel-log-roboto-font-css' ), '1.0.0' );
        wp_add_inline_style( 'travel-log-style', travel_log_generate_custom_color_variables() );

}

add_action( 'enqueue_block_editor_assets', function() {
	wp_enqueue_style( 'travel-log-roboto-font-css', 'https://fonts.googleapis.com/css?family=Roboto:400,500,700' );
	wp_enqueue_style( 'travel-log-editor-style',  get_template_directory_uri() . '/css/editor.css', array( 'travel-log-roboto-font-css' ), '1.0.0' );
} );