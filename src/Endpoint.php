<?php
/**
 * Endpoint
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

/**
 * Endpoint class
 */
abstract class Endpoint {
	/**
	 * Client.
	 * 
	 * @var Client
	 */
	public $client;

	/**
	 * Construct endpoint.
	 * 
	 * @param Client $client Client.
	 */
	public function __construct( $client ) {
		$this->client = $client;
	}
}
