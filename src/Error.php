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
use Throwable;

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
	 * @param object    $data     Data.
	 * @param int       $code     Code.
	 * @param Throwable $previous The previous exception used for the exception chaining.
	 * @return Error
	 * @throws \Exception Throws an exception if response data does not contain an error.
	 */
	public static function from_response_object( $data, $code = 0, $previous = null ) {
		if ( ! \property_exists( $data, 'error' ) ) {
			$exception = new Exception( 'Response object does not contain an error property.', $code, $previous );

			throw $exception;
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
