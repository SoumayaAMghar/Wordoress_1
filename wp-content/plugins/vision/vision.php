<?php
/**
 * Plugin Name:       Vision
 * Plugin URI:        http://avirtum.com/vision-wordpress/
 * Description:       Vision Interactive Map Builder is a lightweight and rich-feature plugin helps you create great interactive maps.
 * Version:           1.5.1
 * Author:            Avirtum
 * Author URI:        http://avirtum.com/
 * License:           GPLv3
 * Text Domain:       vision
 * Domain Path:       /languages
 */
defined('ABSPATH') || exit;

define('VISION_PLUGIN_NAME', 'vision');
define('VISION_PLUGIN_VERSION', '1.5.1');
define('VISION_DB_VERSION', '1.1.0');
define('VISION_SHORTCODE_NAME', 'vision');

/**
 * The code that runs during plugin activation
 */
function vision_activate() {
	require_once( plugin_dir_path( __FILE__ ) . 'includes/activator.php' );
	$activator = new Vision_Activator();
	$activator->activate();
}
register_activation_hook( __FILE__, 'vision_activate' );

/**
 * The code that runs during plugin deactivation
 */
function vision_deactivate() {
	require_once( plugin_dir_path( __FILE__ ) . 'includes/deactivator.php' );
	$deactivator = new Vision_Deactivator();
	$deactivator->deactivate();
}
register_deactivation_hook( __FILE__, 'vision_deactivate' );

/**
 * The code that runs after plugins loaded
 */
function vision_check_db() {
	require_once( plugin_dir_path( __FILE__ ) . 'includes/activator.php' );
	
	$activator = new Vision_Activator();
	$activator->check_db();
}
add_action('plugins_loaded', 'vision_check_db');


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
require_once( plugin_dir_path( __FILE__ ) . 'includes/plugin.php' );


function vision_run() {
	$pluginBasename = plugin_basename(__FILE__);
	
	$plugin = new Vision_Builder($pluginBasename);
	$plugin->run();
}
add_action('init', 'vision_run');