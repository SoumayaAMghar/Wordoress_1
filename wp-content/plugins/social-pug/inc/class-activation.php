<?php
namespace Mediavine\Grow;

use InvalidArgumentException;
use Requests_Exception_HTTP;
use RuntimeException;
use WP_Error;
use WP_HTTP_Requests_Response;

/**
 * Handler for license and activation requirements.
 */
class Activation extends \Social_Pug {

	public const ITEM_ID = 28;

	public const MARKETPLACE_API_BASE_URL = 'https://marketplace.mediavine.com';

	public const LICENSE_STATUS_INVALID = 'invalid';

	public const LICENSE_STATUS_EXPIRED = 'expired';

	public const LICENSE_STATUS_VALID = 'valid';

	public const OPTION_LICENSE_STATUS = 'mv_grow_license_status';

	public const OPTION_LICENSE_STATUS_DATE = 'mv_grow_license_status_date';

	/** @var null  */
	private static $instance = null;

	/**
	 * Make a request to the marketplace API.
	 *
	 * @param array $query Query string to pass as part of the request.
	 * @return array
	 * @throws RuntimeException If an error is encountered during the request.
	 */
	private function api_request( array $query = [] ) : array {
		$query_string = http_build_query( $query );
		$url          = self::MARKETPLACE_API_BASE_URL;
		if ( ! empty( $query_string ) ) {
			$url .= false === strstr( $url, '?' ) ? '?' : '&';
			$url .= $query_string;
		}

		$request_result = wp_remote_get( $url );
		if ( ! is_array( $request_result ) ) {
			if ( $request_result instanceof WP_Error ) {
				throw new RuntimeException( $request_result->get_error_message() );
			} else {
				throw new RuntimeException( 'An unknown error occurred while making a marketplace API request.' );
			}
		}

		/** @var WP_HTTP_Requests_Response $requests_response */
		$requests_response = $request_result['http_response'];
		$requests_response->get_response_object()->throw_for_status();

		$result = json_decode( $requests_response->get_data(), true );
		if ( ! is_array( $result ) ) {
			throw new RuntimeException( 'Unexpected format of marketplace API response.' );
		}

		return $result;
	}

	/**
	 *
	 *
	 * @return Activation|\Social_Pug|null
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 *
	 */
	public function init() {
		add_action( 'update_option_dpsp_settings', [ $this, 'manage_grow_license' ], 10, 2 );
		add_action( 'wp_loaded', [ $this, 'plugin_updated_check' ] );

		register_activation_hook( mv_grow_get_activation_path(), [ $this, 'plugin_activation' ] );
		register_deactivation_hook( mv_grow_get_activation_path(), [ $this, 'plugin_deactivation' ] );
	}

	/**
	 * Runs hook at plugin activation.
	 *
	 * The update hook will run a bit later through its own hook.
	 *
	 * @return void
	 */
	public function plugin_activation() {
		do_action( 'mv_grow_plugin_activated' );
	}

	/**
	 * Runs hook at plugin update.
	 *
	 * This runs after all plugins are loaded so it can run after update. It also performs a
	 * check based on version number, just in case someone updates in a non-conventional way.
	 * After completing hooks, Grow version number is updated in the db.
	 *
	 * @return void
	 */
	public function plugin_updated_check() {
		// Only progress if version has changed
		if ( get_option( 'mv_grow_version' ) === self::$VERSION ) {
			return;
		}

		do_action( 'mv_grow_plugin_updated' );
		update_option( 'mv_grow_version', self::$VERSION );
	}

	/**
	 * Runs hook at plugin deactivation.
	 *
	 * @return void
	 */
	public function plugin_deactivation() {
		do_action( 'mv_grow_plugin_deactivated' );
	}

	/**
	 * Checks to make sure there's a license, and runs relicense if not found.
	 *
	 * @deprecated 2.16 Old license system upgrade. Do not use.
	 * @return void
	 */
	public function relicense_check() {
		if ( ! get_option( 'mv_grow_license' ) ) {
			$this->relicense();
		}
	}

	/**
	 * @deprecated 2.16 Old license system upgrade. Do not use.
	 */
	public function relicense() {
		if ( 'LOCK' === get_transient( 'mv_grow_relicense_lockout' ) ) {
			return;
		}
		set_transient( 'mv_grow_relicense_lockout', 'LOCK', 300 );
		$settings     = get_option( 'dpsp_settings', [] );
		$grow_license = get_option( 'mv_grow_license', false );

		// Remove serial key if it exists, we don't want it exposed
		if ( empty( $settings['product_serial'] ) ) {
			return;
		}

		if ( ! empty( $settings['mv_grow_license'] ) ) {
			return;
		}

		$params = [
			'serial' => $settings['product_serial'],
			'url'    => get_site_url(),
		];

		$url_string = http_build_query( $params );
		$response   = wp_remote_get( 'https://marketplace.mediavine.com/wp-json/mv-edd/v1/convert?' . $url_string );

		if ( ! is_array( $response ) || is_wp_error( $response ) ) {
			return;
		}
		$headers = $response['headers']; // array of http header lines
		$body    = json_decode( $response['body'] ); // use the content
		error_log( print_r( $body, true ) ); // @codingStandardsIgnoreLine
		if ( ! $body ) {
			error_log( 'No Body Response from Marketplace' ); // @codingStandardsIgnoreLine

			return;
		}
		if ( isset( $body->data ) && 401 === $body->data->status ) {
			error_log( 'Access to Marketplace REST API forbidden' ); // @codingStandardsIgnoreLine

			return;
		}
		if ( ! isset( $body->license ) ) {
			error_log( 'Response received but no license in response from Marketplace' ); // @codingStandardsIgnoreLine

			return;
		}
		if ( ! isset( $body->license->license_key ) ) {
			error_log( print_r( $body->license, true ) ); // @codingStandardsIgnoreLine
			error_log( 'License in response but missing actual key.' ); // @codingStandardsIgnoreLine

			return;
		}
		$settings['mv_grow_license'] = $body->license->license_key;
		update_option( 'dpsp_settings', $settings );
		update_option( 'mv_grow_license', $body->license->license_key );
	}

	/**
	 * Set the addon license status.
	 *
	 * @param string|null $license_status Updated license status. Must be one of the LICENSE_STATUS_* constants. Null to delete.
	 * @throws InvalidArgumentException If $license_status is invalid.
	 */
	private function set_license_status( ?string $license_status ) : void {
		if ( null === $license_status ) {
			delete_option( self::OPTION_LICENSE_STATUS );
			delete_option( self::OPTION_LICENSE_STATUS_DATE );
			return;
		}

		$valid_statuses = [
			self::LICENSE_STATUS_EXPIRED => self::LICENSE_STATUS_EXPIRED,
			self::LICENSE_STATUS_INVALID => self::LICENSE_STATUS_INVALID,
			self::LICENSE_STATUS_VALID   => self::LICENSE_STATUS_VALID,
		];
		if ( ! array_key_exists( $license_status, $valid_statuses ) ) {
			throw new InvalidArgumentException( 'Invalid $license_status: ' . $license_status );
		}

		update_option( self::OPTION_LICENSE_STATUS, $license_status );
		update_option( self::OPTION_LICENSE_STATUS_DATE, time() );
	}

	/**
	 * Validate a software license as part of a settings update.
	 *
	 * @param array $old_values Original settings form values.
	 * @param array $new_values Updated settings form values.
	 */
	public function validate_license( $old_values, $new_values ) {
		$grow_license = $new_values['mv_grow_license'] ?? null;
		if ( null === $grow_license ) {
			return;
		}

		try {
			$response = $this->api_request( [
				'edd_action' => 'check_license',
				'item_id'    => self::ITEM_ID,
				'license'    => $grow_license,
				'url'        => get_site_url(),
			] );
		} catch ( \Exception $e ) {
			add_settings_error(
				'mv_grow_license',
				'mv_grow_license_request_error',
				__( 'An error was encountered while verifying the license.', 'mediavine' )
			);
			$this->set_license_status( null );
			return;
		}

		$license_valid = $response['license'] ?? null;
		switch ( $license_valid ) {
			case 'expired':
				$this->set_license_status( self::LICENSE_STATUS_EXPIRED );
				return;
			case 'invalid':
				$this->set_license_status( self::LICENSE_STATUS_INVALID );
				return;
			case 'valid':
				$this->set_license_status( self::LICENSE_STATUS_VALID );
				return;
		}

		$this->set_license_status( null );
		add_settings_error(
			'mv_grow_license',
			'mv_grow_license_invalid',
			__( 'The Grow Social Pro license could not be validated.', 'mediavine' )
		);
	}

	/**
	 * Conditionally verify the license during a settings update, if modified.
	 *
	 * @param array $old_values Original settings form values.
	 * @param array $new_values Updated settings form values.
	 */
	public function manage_grow_license( $old_values, $new_values ) {
		$new_license = $new_values['mv_grow_license'] ?? null;
		$old_license = $old_values['mv_grow_license'] ?? null;

		if ( $old_license !== $new_license ) {
			update_option( 'mv_grow_license', $new_license );

			if ( ! empty( $new_license ) ) {
				$this->validate_license( $old_values, $new_values );
			} else {
				$this->set_license_status( null );
			}
		}
	}
}
