<?php

/**
 * Add custom schedules to use for the cron jobs.
 *
 * @return array
 */
function dpsp_cron_schedules( $schedules ) {
	$schedules['dpsp_2x_hourly'] = [
		'interval' => ( 3600 * 2 ),
		'display'  => __( 'Once every two hours', 'social-pug' ),
	];

	$schedules['weekly'] = [
		'interval' => ( 3600 * 24 * 7 ),
		'display'  => __( 'Once every week', 'social-pug' ),
	];

	return $schedules;
}

/**
 * Set cron jobs. Verifies the serial key.
 *
 * @return void
 */
function dpsp_set_cron_jobs() {
	if ( false === wp_get_schedule( 'dpsp_cron_update_serial_key_status' ) ) {
		wp_schedule_event( time(), 'daily', 'dpsp_cron_update_serial_key_status' );
	}
}

/**
 * Stop cron jobs.
 *
 * @return void
 */
function dpsp_stop_cron_jobs() {
	// Remove deprecated cron
	wp_clear_scheduled_hook( 'dpsp_cron_get_posts_networks_share_count', [ '2x_hourly' ] );
	wp_clear_scheduled_hook( 'dpsp_cron_get_posts_networks_share_count', [ 'daily' ] );
	wp_clear_scheduled_hook( 'dpsp_cron_get_posts_networks_share_count', [ 'weekly' ] );
	wp_clear_scheduled_hook( 'dpsp_cron_get_posts_networks_share_count' );
	wp_clear_scheduled_hook( 'dpsp_cron_update_serial_key_status' );
}

/**
 * Checks the status of the users serial key and updates the returned value
 *
 * @return void
 */
function dpsp_cron_update_serial_key_status() {
	dpsp_update_serial_key_status();
}

/**
 * Disables old unused cron jobs and enables the new ones
 *
 * @return void
 */
function dpsp_cron_disable_old_crons( $old_plugin_version = '', $new_plugin_version = '' ) {
	// In version 1.6.0 the cron job handling social shares was deprecated and
	// three new cron jobs were added
	if ( -1 !== version_compare( $new_plugin_version, '1.6.0' ) ) {
		// Stop cron jobs
		dpsp_stop_cron_jobs();

		// Add new cron jobs
		dpsp_set_cron_jobs();
	}
}

/**
 * Register hooks for functions-cron.php
 */
function dpsp_register_functions_cron() {
	// Not sure what this sniff is going on about: Detected changing of cron_schedules, but could not detect the interval value.
	add_filter( 'cron_schedules', 'dpsp_cron_schedules' ); // @codingStandardsIgnoreLine — WordPress.VIP.CronInterval.ChangeDetected
	add_action( 'dpsp_cron_update_serial_key_status', 'dpsp_cron_update_serial_key_status' );
	add_action( 'dpsp_update_database', 'dpsp_cron_disable_old_crons', 10, 2 );
}
