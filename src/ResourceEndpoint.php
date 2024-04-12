<?php
/**
 * Resource endpoint
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Resource endpoint class
 */
abstract class ResourceEndpoint extends Endpoint {
	/**
	 * Administration ID.
	 * 
	 * @var string
	 */
	public $administration_id;

	/**
	 * Construct resource endpoint.
	 * 
	 * @param Client $client            Client.
	 * @param string $administration_id Administration ID.
	 */
	public function __construct( $client, $administration_id ) {
		parent::__construct( $client );

		$this->administration_id = $administration_id;
	}

	/**
	 * Get API URL.
	 *
	 * @param string $resource_path Resource path.
	 * @return string
	 */
	public function get_api_url( $resource_path ) {
		return \strtr(
			'https://moneybird.com/api/:version/:administration_id/:resource_path.:format',
			[
				':version'           => 'v2',
				':administration_id' => $this->administration_id,
				':resource_path'     => $resource_path,
				':format'            => 'json',
			]
		);
	}

	/**
	 * Post data.
	 * 
	 * @param string $resource_path Resource path.
	 * @param mixed  $data          Data.
	 * @return mixed
	 */
	public function post( $resource_path, $data ) {
		$api_url = $this->get_api_url( $resource_path );

		$response = Http::post(
			$api_url,
			[
				'headers' => [
					'Authorization' => 'Bearer ' . $this->client->api_token,
					'Content-Type'  => 'application/json',
				],
				'body'    => \wp_json_encode( $data ),
			]
		);

		return $response;
	}
}
