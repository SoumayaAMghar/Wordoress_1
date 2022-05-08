<?php
/**
 * Plugin Name:         Grow Social by Mediavine
 * Plugin URI:          https://marketplace.mediavine.com/grow-social-pro/
 * Description:         Add beautiful social sharing buttons to your posts, pages and custom post types.
 * Version:             1.20.1

 * Requires at least:   5.2
 * Requires PHP:        7.1
 * Author:              Mediavine
 * Text Domain:         social-pug
 * Author URI:          https://marketplace.mediavine.com/grow-social-pro/
 * License:             GPL2
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This plugin requires WordPress' );
}

require_once __DIR__ . '/inc/functions-requirements.php';

if ( ! mv_grow_is_compatible() ) {
	add_action( 'admin_notices', 'mv_grow_incompatible_notice' );
	add_action( 'admin_head', 'mv_grow_throw_warnings' );
	return false;
}

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/constants.php';

/**
 * Returns plugin activation path. Here for backwards compatibility.
 *
 * @return string
 */
function mv_grow_get_activation_path() {
	return __FILE__;
}

Social_Pug::get_instance();
