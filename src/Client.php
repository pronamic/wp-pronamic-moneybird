<?php
/**
 * Client
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use Pronamic\WordPress\Http\Facades\Http;

/**
 * Client class
 */
final class Client {
	/**
	 * API token.
	 * 
	 * @var string
	 */
	public $api_token;

	/**
	 * Construct client.
	 * 
	 * @param string $api_token API token.
	 */
	public function __construct( $api_token ) {
		$this->api_token = $api_token;
	}

	/**
	 * Get administration endpoint.
	 *
	 * @param string $administration_id Administration ID.
	 * @return AdministrationEndpoint
	 */
	public function get_administration_endpoint( $administration_id ) {
		return new AdministrationEndpoint( $this, $administration_id );
	}

	/**
	 * Get headers.
	 * 
	 * @return array
	 */
	private function get_headers() {
		return [
			'Authorization' => 'Bearer ' . $this->api_token,
			'Content-Type'  => 'application/json',
			'Time-Zone'     => 'UTC',
		];
	}

	/**
	 * Ensure response status.
	 * 
	 * @param Response    $response                 Response.
	 * @param null|string $expected_response_status Expected response status.
	 * @throws Error Throws an exception if the response status does not meet expectations.
	 */
	public function ensure_response_status( $response, $expected_response_status ) {
		if ( null == $expected_response_status ) {
			return;
		}

		$response_status = (string) $response->status();

		if ( $expected_response_status === $response_status ) {
			return;
		}

		$http_exception = new \Exception( 'Unexpected HTTP response: ' . $response_status, (int) $response_status );

		$response_data = $response->json();

		$error = Error::from_response_object( $response_data, (int) $response_status, $http_exception );

		throw $error;
	}

	/**
	 * Get data.
	 * 
	 * @param string      $api_url    API URL.
	 * @param array       $parameters Parameters.
	 * @param null|string $expected_response_status Expected response status.
	 * @return mixed
	 */
	public function get( $api_url, $parameters, $expected_response_status = null ) {
		$api_url .= '?' . \http_build_query( $parameters, '', '&' );

		$response = Http::get(
			$api_url,
			[
				'headers' => $this->get_headers(),
			]
		);

		$this->ensure_response_status( $response, $expected_response_status );

		return $response;
	}

	/**
	 * Post data.
	 * 
	 * @param string      $api_url API URL.
	 * @param mixed       $data    Data.
	 * @param null|string $expected_response_status Expected response status.
	 * @return mixed
	 */
	public function post( $api_url, $data, $expected_response_status = null ) {
		$response = Http::post(
			$api_url,
			[
				'headers' => $this->get_headers(),
				'body'    => \wp_json_encode( $data ),
			]
		);

		$this->ensure_response_status( $response, $expected_response_status );

		return $response;
	}
}
