<?php

namespace Mediavine\Grow;

use Exception;
use InvalidArgumentException;
use Requests;
use Requests_Exception;
use UnexpectedValueException;
use WP_Error;
use WP_HTTP_Requests_Response;

/**
 * API client for fetching and refreshing URL share counts.
 */
class Share_Count_Client {

	private const CONSUMER = 'grow-social-pro';

	private const API_BASE_URL = 'https://share-count-api.grow.me';

	/** @var string */
	private $license;

	/** @var string */
	private $site_url;

	/**
	 * Set up the client.
	 *
	 * @param string $site_url Full base URL of the site requesting share counts (e.g. https://www.example.com).
	 * @param string $license License key for Grow Pro Social.
	 */
	public function __construct( string $site_url, string $license ) {
		$this->site_url = $site_url;
		$this->license  = $license;
	}

	/**
	 * Given a relative URL, get an absolute URL.
	 *
	 * @param string $url Relative API URL.
	 * @return string
	 */
	public function api_url( string $url ) : string {
		$url = trim( $url, '/' );
		return self::API_BASE_URL . '/' . $url;
	}

	/**
	 * Perform an API request.
	 *
	 * @param string     $method One of the Requests::* verb constants.
	 * @param string     $url Relative API URL.
	 * @param array|null $body Body of the request.
	 * @param array      $headers Custom headers to be sent with the request.
	 * @return WP_HTTP_Requests_Response
	 * @throws InvalidArgumentException If the HTTP method is unsupported.
	 * @throws Requests_Exception If an error is encountered during the HTTP request.
	 * @throws UnexpectedValueException If an error is encountered while reading the HTTP request response.
	 */
	private function api_request( string $method, string $url, ?array $body = null, array $headers = [] ) : WP_HTTP_Requests_Response {
		$valid_methods = [ Requests::DELETE, Requests::HEAD, Requests::GET, Requests::PATCH, Requests::POST ];
		if ( ! in_array( $method, $valid_methods, true ) ) {
			// @codeCoverageIgnoreStart
			throw new InvalidArgumentException( "Invalid HTTP request method: {$method}" );
			// @codeCoverageIgnoreEnd
		}

		// This is a JSON API. We'll always communicate via JSON.
		$headers['Content-Type'] = 'application/json; charset=utf-8';

		$url = self::api_url( $url );

		$request_args = [
			'headers' => $headers,
			'method'  => $method,
		];
		if ( is_array( $body ) ) {
			$encoded_body = json_encode( $body );
			if ( false === $encoded_body ) {
				// @codeCoverageIgnoreStart
				throw new UnexpectedValueException( 'Failed to encode request body.' );
				// @codeCoverageIgnoreEnd
			}
			$request_args['body'] = $encoded_body;
		}

		$response = wp_remote_request(
			$url,
			$request_args
		);
		if ( $response instanceof WP_Error ) {
			throw new Requests_Exception(
				$response->get_error_message(),
				'wp_http.' . $response->get_error_code()
			);
		} elseif ( ! is_array( $response ) ) {
			// @codeCoverageIgnoreStart
			throw new UnexpectedValueException( 'Unexpected response type from API request.' );
			// @codeCoverageIgnoreEnd
		}

		if ( ! array_key_exists( 'http_response', $response ) || ! ( $response['http_response'] instanceof WP_HTTP_Requests_Response ) ) {
			// @codeCoverageIgnoreStart
			throw new UnexpectedValueException( 'Unexpected HTTP response object type.' );
			// @codeCoverageIgnoreEnd
		}

		// Create a copy of the response, so it can be modified without affecting the underlying objects/references.
		$request_response_clone = clone $response['http_response']->get_response_object();
		$response_array         = $response['http_response']->to_array();
		$response_object        = new WP_HTTP_Requests_Response(
			$request_response_clone,
			$response_array['filename'] ?? ''
		);

		if ( is_string( $response_object->get_data() ) ) {
			$decoded_data = json_decode( $response_object->get_data(), true );
			if ( false !== $decoded_data ) {
				$response_object->set_data( $decoded_data );
			}
		}

		return $response_object;
	}

	/**
	 * Get share counts for a URL.
	 *
	 * @param Share_Count_URL[] $urls An array of URLs for which to retrieve counts.
	 * @param bool              $combine Whether the API should combine results from all requested urls into a single result.
	 * @param bool              $force Whether we should force-refresh the share counts or use cached values.
	 *
	 * @return Share_Count_Url_Counts[]|null
	 * @throws InvalidArgumentException If URL array does not contain exclusively Share_Count_URL.
	 */
	public function fetch_counts( array $urls, bool $combine = false, bool $force = false ) : ?array {
		foreach ( $urls as $url ) {
			if ( ! ( $url instanceof Share_Count_URL ) ) {
				throw new InvalidArgumentException( 'URLs contains an invalid value.' );
			}
		}

		try {
			$response = $this->api_request(
				Requests::POST,
				'shares',
				[
					'combine'  => $combine,
					'consumer' => self::CONSUMER,
					'force'    => $force,
					'license'  => $this->license,
					'site_url' => $this->site_url,
					'urls'     => $urls,
				]
			);

			// Very basic check for a 2xx success status code.
			if ( $response->get_status() < 200 || $response->get_status() > 299 ) {
				return null;
			}

			$response_body = $response->get_data();
			if ( ! is_array( $response_body ) ) {
				return null;
			}

			$result = [];
			foreach ( $response_body as $lookup_url => $totals ) {
				$result[ $lookup_url ] = new Share_Count_Url_Counts( $totals );
			}
		} catch ( Exception $e ) {
			return null;
		}

		return $result;
	}

	/**
	 * Force a refresh of share counts for a given URL.
	 *
	 * @param string $url URL to refresh updated share count totals.
	 *
	 * @return Share_Count_Url_Counts|null
	 */
	public function refresh_counts( string $url ) : ?Share_Count_Url_Counts {
		try {
			$response = $this->api_request(
				Requests::POST,
				'shares/refresh',
				[
					'consumer' => self::CONSUMER,
					'license'  => $this->license,
					'site_url' => $this->site_url,
					'url'      => $url,
				]
			);

			$response_body = $response->get_data();
			if ( ! is_array( $response_body ) ) {
				return null;
			}

			$result = new Share_Count_Url_Counts( $response_body[ $url ] ?? [] );
		} catch ( Exception $e ) {
			return null;
		}

		return $result;
	}
}
