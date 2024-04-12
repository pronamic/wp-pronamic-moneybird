<?php
/**
 * Date
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-2.0-or-later
 * @package   Pronamic\Moneybird
 */

namespace Pronamic\Moneybird;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use JsonSerializable;

/**
 * Date class
 */
final class Date extends DateTimeImmutable implements JsonSerializable, RemoteSerializable {
	/**
	 * Date from string.
	 * 
	 * @param string $value Value.
	 * @return Date
	 * @throws \Exception Throws an exception if string has unexpected format.
	 */
	public static function from_string( $value ) {
		$result = self::createFromFormat( 'Y-m-d', $value, new DateTimeZone( 'UTC' ) );

		if ( false === $result ) {
			throw new Exception( 'Unexteded date format: ' . wp_json_encode( $value ) );
		}

		$result = $result->setTime( 0, 0 );

		return $result;
	}

	/**
	 * JSON serialize.
	 * 
	 * @return mixed
	 */
	public function jsonSerialize(): mixed {
		return $this->__toString();
	}

	/**
	 * Remote serialize.
	 * 
	 * @param string $context Context.
	 * @return mixed
	 */
	public function remote_serialize( $context = '' ) {
		return $this->__toString();
	}

	/**
	 * To string.
	 * 
	 * @return string
	 */
	public function __toString() {
		return $this->format( 'Y-m-d' );
	}
}
