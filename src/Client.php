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
	private $api_token;

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
	 * Post data.
	 * 
	 * @param mixed $data Data.
	 * @return mixed
	 */
	public function post( $api_url, $data ) {
		$response = Http::post(
			$api_url,
			[
				'headers' => [
					'Authorization' => 'Bearer ' . $this->api_token,
					'Content-Type'  => 'application/json',
					'Time-Zone'     => 'UTC',
				],
				'body'    => \wp_json_encode( $data ),
			]
		);

		return $response;
	}
}
