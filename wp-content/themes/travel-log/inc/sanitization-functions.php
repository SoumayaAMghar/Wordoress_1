<?php
/**
 * Travel Log sanitization  functions.
 *
 * @package storefront
 */

if ( ! function_exists( 'travel_log_sanitize_choices' ) ) :

	/**
	 * Sanitizes choices (selects / radios).
	 * Checks that the input matches one of the available choices.
	 *
	 * @param array $input the available choices.
	 * @param array $setting the setting object.
	 * @return array
	 */
	function travel_log_sanitize_choices( $input, $setting ) {
		// Ensure input is a slug.
		$input = sanitize_key( $input );

		// Get list of choices from the control associated with the setting.
		$choices = $setting->manager->get_control( $setting->id )->choices;

		// If the input is a valid key, return it; otherwise, return the default.
		return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
	}

endif;

if ( ! function_exists( 'travel_log_sanitize_checkbox' ) ) :
	/**
	 * [travel_log_sanitize_checkbox Sanitize Checkbox]
	 * @param  [type] $checked [description]
	 * @return [type]          [description]
	 */
	function travel_log_sanitize_checkbox( $checked ) {
		return ( ( isset( $checked ) && true == $checked ) ? true : false );
	}

endif;

if ( ! function_exists( 'travel_log_sanitize_category' ) ) :

	/**
	 * [travel_log_sanitize_category Sanitize Category Value]
	 * @param  [type] $input [description]
	 * @return [type]        [description]
	 */
	function travel_log_sanitize_category( $input ) {
		$categories = get_categories();
		$cats = array();
		$i = 0;
		foreach ( $categories as $category ) {
			if($i==0){
				$default = $category->slug;
				$i++;
			}
			$cats[ $category->slug ] = $category->name;
		}
		$valid = $cats;

		if ( array_key_exists( $input, $valid ) ) {
			return $input;
		} else {
			return '';
		}
	}

endif;

if ( ! function_exists( 'travel_log_sanitize_integer' ) ) :
	/**
	 * [travel_log_sanitize_integer Sanitize Intezer Value]
	 * @param  [type] $input [description]
	 * @return [INT]        [description]
	 */
	function travel_log_sanitize_integer( $input ) {
		return absint( $input );
	}

endif;

if ( ! function_exists( 'travel_log_sanitize_multiple_dropdown_taxonomies' ) ) :

	/**
	 * [travel_log_sanitize_multiple_dropdown_taxonomies description]
	 * @param  [type] $input [description]
	 * @return [type]        [description]
	 */
	function travel_log_sanitize_multiple_dropdown_taxonomies( $input ) {
		// Make sure we have array.
		$input = (array) $input;

		// Sanitize each array element.
		$input = array_map( 'absint', $input );

		// Remove null elements.
		$input = array_values( array_filter( $input ) );

		return $input;
	}
endif;


