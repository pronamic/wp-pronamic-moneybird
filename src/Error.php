<?php
/**
 * Error
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use Exception;

/**
 * Error class
 */
final class Error extends Exception {
	/**
	 * Details.
	 * 
	 * @var null|object|string
	 */
	public $details;

	/**
	 * Error from response object.
	 * 
	 * @return Error
	 */
	public static function from_response_object( $data, $code = 0, $previous = null ) {
		if ( ! \property_exists( $data, 'error' ) ) {
			throw new Exception( 'Response object does not contain an error property.', $code, $previous );
		}

		$messages = [
			'Moneybird error from API',
		];

		if ( \is_string( $data->error ) ) {
			$messages[] = $data->error;
		}

		if ( \is_object( $data->error ) ) {
			$messages[] = \wp_json_encode( $data->error );
		}

		$error = new self( \implode( ' · ', $messages ), $code, $previous );

		$error->details = $data->error;

		return $error;
	}
}
