<?php
/**
 * File to load different functions files.
 *
 * @package Travel_Log
 */

/**
 *  New itinerary helper functions.
 *
 * @since 1.2.9
 */
require get_template_directory() . '/inc/itinerary-helpers.php';

/**
 *  Core Functions.
 */
require get_template_directory() . '/inc/core.php';
/**
 *  Widget areas.
 */
require get_template_directory() . '/inc/widget-area.php';

/**
 *  Default theme values.
 */
require get_template_directory() . '/inc/customizer/default-values.php';

/**
 *  Assets functions.
 */
require get_template_directory() . '/inc/assets.php';

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Sanitization functions for this theme.
 */
require get_template_directory() . '/inc/sanitization-functions.php';

/**
 * Custom template function for this theme.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Home template function for this theme.
 */
require get_template_directory() . '/inc/home-template-functions.php';

/**
 * Single post template functions.
 */
include get_template_directory() . '/inc/single-template-functions.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer callbacks
 */
require get_template_directory() . '/inc/customizer/callbacks.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load breadcrumb-class file.
 */
require get_template_directory() . '/inc/breadcrumb-class.php';

/**
* TGM plugin additions.
*/
require get_template_directory() . '/inc/tgm-plugin/tgmpa-hook.php';

/**
* Load Theme Options Metabox.
*/
require get_template_directory() . '/inc/metabox/metabox-load.php';

/**
* Admin Theme info Page.
*/

if ( is_admin() ) {

	require get_template_directory() . '/inc/admin/admin.php';

}