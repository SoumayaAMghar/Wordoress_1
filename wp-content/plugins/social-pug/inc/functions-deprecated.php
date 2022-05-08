<?php

use Mediavine\Grow\Share_Counts;
use Mediavine\Grow\Networks;

/**
 * Originally from functions-mobile.php
 * Used to filter out WhatsApp from showing on desktop
 * removed in favor of using media queries
 * Public function but unlikely to be used
 * @since 2.15.0
 * @param array $settings
 * @param $action
 * @param $location
 *
 * @return array
 * @deprecated 2.15.0
 */
function dpsp_handle_mobile_only_networks( $settings, $action, $location ) {
	return $settings;
}

/**
 * Refreshes the share counts if the share counts cache has expired.
 *
 * @return bool
 * @deprecated 2.16.0
 */
function dpsp_refresh_post_share_counts() {
	return Share_Counts::get_instance()->check_share_counts();
}


/**
 * Checks to see if the post's share counts were updated recently or not.
 *
 * @deprecated 2.16.0
 * @param WP_Post $post_obj
 * @return bool
 */
function dpsp_is_post_share_counts_cache_expired( $post_obj ) {
	return Share_Counts::is_post_count_expired( $post_obj );
}

/**
 * @deprecated 2.16.0
 */
function dpsp_invalidate_all_share_counts() {
	Share_Counts::invalidate_all();
}

/**
 * Returns an array with the saved shares from the database.
 *
 * @param $post_id
 * @return array
 * @deprecated 2.16.0
 */
function dpsp_get_post_share_counts( $post_id = 0 ) {
	return Share_Counts::post_share_counts( $post_id );
}

/**
 * Updates the given share counts for a post into the database.
 *
 * @param int $post_id - the id of the post to save the shares
 * @param array $updated_share_counts - an array with the network shares and total shares
 * @return bool
 * @deprecated 2.16.0
 */
function dpsp_update_post_share_counts( $post_id = 0, $updated_share_counts = [] ) {
	if ( empty( $post_id ) || empty( $updated_share_counts ) ) {
		return false;
	}

	$share_counts     = Share_Counts::get_instance();
	$post             = get_post( $post_id );
	$formatted_shares = [];
	foreach ( $updated_share_counts as $network_slug => $count ) {
		$formatted_shares[ $network_slug . '_share_count' ] = $count;
	}
	$updated_shares = new \Mediavine\Grow\Share_Count_Url_Counts( $formatted_shares );
	$share_counts->update_post_share_counts( $post, $updated_shares );

	return true;
}

/**
 * Updates the top shared posts array.
 *
 * @param int $post_id - the id of the post to save the shares
 * @param array $share_counts - an array with the network shares and total shares
 * @return bool
 * @deprecated 1.16.0
 */
function dpsp_update_top_shared_posts( $post_id = 0, $share_counts = [] ) {
	return Share_Counts::update_top_shared_posts( $post_id, $share_counts );
}

/**
 * Return total share count calculated for the social networks passed, if no social network is passed
 * the total share value will be calculated for all active networks.
 *
 * @param int $post_id - the id for the post to get
 * @param array $networks - the networks for which we want to return the total count
 * @param string $location - the location of the share buttons
 * @return int
 * @deprecated 2.16.0
 */
function dpsp_get_post_total_share_count( $post_id = 0, $networks = [], $location = '' ) {
	if ( 0 === $post_id ) {
		$post_obj = dpsp_get_current_post();
		$post_id  = $post_obj->ID;
	}

	return Share_Counts::post_total_share_counts( $post_id, $location );
}

/**
 * Checks to see if total shares are at least as high as the minimum count
 * needed. Return null if the minimum shares is greater than the total.
 *
 * @param $total_shares - the total shares of the post for all active networks
 * @param $post_id - the ID of the post
 * @param $location - the location where the buttons are displayed
 * @return mixed int|null
 * @deprecated 2.16.0
 */
function dpsp_post_total_share_count_minimum_count( $total_shares, $post_id, $location ) {
	return Share_Counts::post_total_shares_minimum( $total_shares, $post_id, $location );
}

/**
 * Transients have proved to be unreliable for Facebook App tokens,
 * so we've moved them over to options.
 *
 * This function migrates the value saved in the Facebook App token transient to an option.
 */
function dpsp_migrate_facebook_access_token_transient_to_option() {
	// Get the access token saved in transient
	$facebook_access_token = get_transient( 'dpsp_facebook_access_token' );

	// If the transient value doesn't exit, no need to do anything
	if ( empty( $facebook_access_token ) ) {
		return false;
	}

	// Add the transient value as an option
	update_option( 'dpsp_facebook_access_token', $facebook_access_token );

	// Delete the transient value altogether
	delete_transient( 'dpsp_facebook_access_token' );
}

/**
 * Returns the share count saved for a post given the post_id and the
 * network we wish to retreive the value for.
 *
 * @param int    $post_id            - id of the post
 * @param string $network_slug - slug of the social network
 * @return mixed                - bool false if something went wrong, and int if everything went well
 * @deprecated 2.16.0
 */
function dpsp_get_post_share_count( $post_id, $network_slug ) {
	if ( empty( $post_id ) && empty( $network_slug ) ) {
		return false;
	}

	$shares = Share_Counts::post_share_counts( $post_id );
	if ( isset( $shares[ $network_slug ] ) && in_array( $network_slug, dpsp_get_networks_with_social_count(), true ) ) {
		return $shares[ $network_slug ];
	} else {
		return false;
	}
}

/**
 * Rounds the share counts.
 *
 * @param int|array $share_count
 * @param string $location
 * @return int
 * @deprecated 2.16.0
 */
function dpsp_round_share_counts( $share_count, $location = '' ) {
	if ( empty( $location ) ) {
		return $share_count;
	}

	if ( empty( $share_count ) ) {
		return $share_count;
	}

	$location_settings = Mediavine\Grow\Settings::get_setting( 'dpsp_location_' . $location, [] );

	if ( ! isset( $location_settings['display']['count_round'] ) ) {
		return $share_count;
	}

	/**
	 * Filter the precision at which the number should be rounded.
	 *
	 * @param int $round_precision
	 */
	$round_precision = apply_filters( 'dpsp_share_counts_round_precision', 1 );

	$share_count = Share_Counts::round_counts( $share_count, $location, $round_precision );

	return $share_count;
}

/**
 * Used to register share count hooks, now taken care of inside the class
 *
 * @return bool
 * @deprecated 2.16.0
 */
function dpsp_register_functions_share_counts() {
	return false;
}


